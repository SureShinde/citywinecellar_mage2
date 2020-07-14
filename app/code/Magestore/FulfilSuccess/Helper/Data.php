<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Helper;

/**
 * Class \Magestore\FulfilSuccess\Helper\Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Const scanning barcode
     */
    const BARCODE_ATTRIBUTE_CONFIG_PATH = "fulfilsuccess/scanning/barcode";

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     *
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrlBuilder;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Backend\Model\UrlInterface $backendUrlBuilder
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrlBuilder
    ) {
        parent::__construct($context);
        $this->_backendUrlBuilder = $backendUrlBuilder;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Get backend url
     *
     * @param string $path
     * @param array $params
     * @return string
     */
    public function getBackendUrl($path, $params = [])
    {
        return $this->_backendUrlBuilder->getUrl($path, $params);
    }

    /**
     * Get setup guide url
     *
     * @return string
     */
    public function getSetupGuideUrl()
    {
        return $this->getBackendUrl('adminhtml/system_config/edit/section/fulfilsuccess')
            . '#fulfilsuccess_guide-link';
    }

    /**
     * Get store config
     *
     * @param string $path
     * @return string
     */
    public function getStoreConfig($path)
    {
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get product barcode attribute
     *
     * @return string
     */
    public function getProductBarcodeAttribute()
    {
        return $this->getStoreConfig(self::BARCODE_ATTRIBUTE_CONFIG_PATH);
    }

    /**
     * Get product barcode
     *
     * @param int $productId
     * @return string
     */
    public function getProductBarcodes($productId)
    {
        $barcodes = [];
        $barcodeAttribute = $this->getProductBarcodeAttribute();
        if ($productId) {
            if ($barcodeAttribute) {
                $product = $this->_objectManager->create(\Magento\Catalog\Model\Product::class);
                $resource = $this->_objectManager->create(\Magento\Catalog\Model\ResourceModel\Product::class);
                $resource->load($product, $productId);
                if ($product->getId() && $product->getData($barcodeAttribute)) {
                    $barcodes[] = $product->getData($barcodeAttribute);
                }
            }
            if ($this->isModuleOutputEnabled('Magestore_BarcodeSuccess')) {
                $barcodeCollection = $this->_objectManager
                    ->create(\Magestore\BarcodeSuccess\Model\ResourceModel\Barcode\Collection::class);
                if ($barcodeCollection) {
                    $barcodeCollection->addFieldToFilter(
                        \Magestore\BarcodeSuccess\Api\Data\BarcodeInterface::PRODUCT_ID,
                        $productId
                    );
                    $barcodeCollection->addFieldToSelect(\Magestore\BarcodeSuccess\Api\Data\BarcodeInterface::BARCODE);
                    $osBarcodes = $barcodeCollection->getColumnValues(
                        \Magestore\BarcodeSuccess\Api\Data\BarcodeInterface::BARCODE
                    );
                    if (!empty($osBarcodes)) {
                        $barcodes = array_unique(array_merge_recursive($barcodes, $osBarcodes));
                    }
                }
            }
        }
        return implode('||', $barcodes);
    }
}
