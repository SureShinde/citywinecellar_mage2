<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model\Catalog\Highlight;

use Amasty\Rewards\Api\CatalogHighlightManagementInterface;
use Amasty\Rewards\Api\Data\HighlightInterfaceFactory;
use Amasty\Rewards\Helper\Data;
use Amasty\Rewards\Api\RuleRepositoryInterface;
use Amasty\Rewards\Api\Data\HighlightInterface;
use Amasty\Rewards\Model\Calculation;
use Amasty\Rewards\Model\Config;
use Magento\Store\Model\StoreManagerInterface;

class Management implements CatalogHighlightManagementInterface
{
    /**
     * @var array
     */
    private $amounts = [];

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Calculation
     */
    private $calculation;

    /**
     * @var Validator
     */
    private $highlightValidator;

    /**
     * @var HighlightInterfaceFactory
     */
    private $highlightFactory;

    /**
     * @var ValidObjectFromDataFactory
     */
    private $objectFactory;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Config $config,
        Calculation $calculation,
        Validator $highlightValidator,
        HighlightInterfaceFactory $highlightFactory,
        ValidObjectFromDataFactory $objectFactory,
        RuleRepositoryInterface $ruleRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->calculation = $calculation;
        $this->highlightValidator = $highlightValidator;
        $this->highlightFactory = $highlightFactory;
        $this->objectFactory = $objectFactory;
        $this->ruleRepository = $ruleRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getHighlightForProduct($productId, $customerId, $attributes = null)
    {
        $storeId = $this->storeManager->getStore()->getId();
        /** @var ValidObject $validObject */
        $validObject = $this->objectFactory->create($productId, $attributes, $customerId, $storeId);
        $pageVisibility = $this->config->getHighlightProductVisibility($storeId);
        $color = $this->config->getHighlightColor($storeId);

        return $this->commonHighlight($validObject, $pageVisibility, $color);
    }

    /**
     * {@inheritdoc}
     */
    public function getHighlightForCategory($productId, $customerId, $attributes = null)
    {
        $storeId = $this->storeManager->getStore()->getId();
        /** @var ValidObject $validObject */
        $validObject = $this->objectFactory->create($productId, $attributes, $customerId, $storeId);
        $pageVisibility = $this->config->getHighlightCategoryVisibility($storeId);
        $color = $this->config->getHighlightColor($storeId);

        return $this->commonHighlight($validObject, $pageVisibility, $color);
    }

    /**
     * @param ValidObject $validObject
     * @param bool $pageVisibility
     * @param string $color
     *
     * @return HighlightInterface
     */
    private function commonHighlight($validObject, $pageVisibility, $color)
    {
        $data = [
            HighlightInterface::VISIBLE => $pageVisibility
                && $this->calculateAmount($validObject),
            HighlightInterface::CAPTION_COLOR => $color,
            HighlightInterface::CAPTION_TEXT => $this->calculateAmount($validObject) . __(' points')
        ];

        return $this->highlightFactory->create()->setData($data);
    }

    /**
     * @param ValidObject $validObject
     *
     * @return float
     */
    private function calculateAmount($validObject)
    {
        if (empty($this->amounts[$validObject->getProduct()->getId()])) {
            $amount = 0;

            $rules = $this->ruleRepository->getRulesByAction(
                Data::MONEY_SPENT_ACTION,
                $validObject->getCustomer()->getWebsiteId(),
                $validObject->getCustomer()->getGroupId()
            );

            /** @var \Amasty\Rewards\Api\Data\RuleInterface $rule */
            foreach ($rules as $rule) {
                if ($this->highlightValidator->validate($rule, $validObject)) {
                    $amount += $this->calculation->calculatePointsByProduct(
                        $validObject->getProductCandidates(),
                        $validObject->getCustomer()->getId(),
                        $rule
                    );
                }
            }

            $this->amounts[$validObject->getProduct()->getId()] = floatval(round($amount, 2));
        }

        return $this->amounts[$validObject->getProduct()->getId()];
    }
}
