<?php

namespace Laconica\Analytics\Rewrite\Checkout\Controller\Cart;

use Laconica\Analytics\Helper\Config;
use Magento\Framework\Exception\NoSuchEntityException;

class Add extends \Magento\Checkout\Controller\Cart\Add
{

    /**
     * Resolve response
     *
     * @param string $backUrl
     * @param \Magento\Catalog\Model\Product $product
     * @return $this|\Magento\Framework\Controller\Result\Redirect
     */
    protected function goBack($backUrl = null, $product = null)
    {
        if (!$this->getRequest()->isAjax()) {
            return parent::_goBack($backUrl);
        }

        $result = [];

        if ($backUrl || $backUrl = $this->getBackUrl()) {
            $result['backUrl'] = $backUrl;
        } else {
            if ($product && !$product->getIsSalable()) {
                $result['product'] = [
                    'statusText' => __('Out of stock')
                ];
            }
        }
        // CUSTOM CODE
        $enabled = $this->_scopeConfig->getValue(Config::GTM_ENABLED_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $productSimple = $this->getProduct();
        $product = ($productSimple) ? $productSimple : $product;
        if ($product && $enabled) {
            $result['dataLayer'] = [
                'event' => 'addToBag',
                'product' => [
                    'sku' => $product->getSku(),
                    'name' => $product->getName(),
                    'price' => $product->getFinalPrice(),
                    'color' => (string)$product->getAttributeText('color'),
                    'size' => (string)$product->getAttributeText('size')
                ]
            ];
        }
        // END CUSTOM CODE
        $this->getResponse()->representJson(
            $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result)
        );
    }

    protected function getProduct(){
        $productId = $this->getRequest()->getParam('selected_configurable_option', false);
        if ($productId) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }
}