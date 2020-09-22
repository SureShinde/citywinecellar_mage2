<?php

namespace Laconica\Checkout\Plugin;

class StateFilter
{
    /**
     * @var \Laconica\Checkout\Helper\StateConfig stateConfigHelper
     */
    private $stateConfigHelper;

    /**
     * @var \Magento\Checkout\Model\Session $checkoutSession
     */
    private $checkoutSession;

    private $canShowSpecific = false;

    public function __construct(
        \Laconica\Checkout\Helper\StateConfig $stateConfigHelper,
        \Magento\Checkout\Model\Session $checkoutSession
    )
    {
        $this->stateConfigHelper = $stateConfigHelper;
        $this->checkoutSession = $checkoutSession;
        $this->canShowSpecific = $this->canShowSpecificStates();
    }

    public function afterToOptionArray(
        \Magento\Directory\Model\ResourceModel\Region\Collection $subject,
        $options
    )
    {
        if (!$this->stateConfigHelper->isEnabled()) {
            return $options;
        }

        $allowedUsStates = $this->stateConfigHelper->getCommonAllowedStates();

        if ($this->canShowSpecific) {
            $specificStates = $this->stateConfigHelper->getSpecificAllowedStates();
            $allowedUsStates = array_merge($allowedUsStates, $specificStates);
        }

        $result[] = (is_array($options) && isset($options[0])) ? array_shift($options) : [];

        foreach ($options as $option) {
            if (isset($option['value']) && in_array($option['value'], $allowedUsStates)) {
                array_push($result, $option);
            }
        }

        return $result;
    }


    /**
     * Check if exist product in cart form invalid category
     *
     * @return bool
     */
    private function canShowSpecificStates()
    {
        $cartItems = $this->checkoutSession->getQuote()->getAllItems();
        $inValidCategories = $this->stateConfigHelper->getInValidCategories();

        foreach ($cartItems as $item) {
            /** @var \Magento\Quote\Model\Quote\Item $item */

            $product = $item->getProduct();

            if (!$product || !$product->getId()) {
                continue;
            }

            $categoriesIds = $product->getCategoryIds();

            if (!$categoriesIds) {
                continue;
            }

            foreach ($categoriesIds as $categoryId) {
                if (in_array($categoryId, $inValidCategories)) {
                    return false;
                }
            }
        }
        return true;
    }
}