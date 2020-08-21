<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Helper;

/**
 * Class Data
 *
 * Used to create Helper data
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $_random;

    /**
     * @var \Magestore\BarcodeSuccess\Model\ResourceModel\Barcode
     */
    public $resource;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     *
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     *
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrlBuilder;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Math\Random $random
     * @param \Magestore\BarcodeSuccess\Model\ResourceModel\Barcode $barcodeResource
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Backend\Model\UrlInterface $backendUrlBuilder
     * @param \Magestore\BarcodeSuccess\Model\Locator\LocatorInterface $locator
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Math\Random $random,
        \Magestore\BarcodeSuccess\Model\ResourceModel\Barcode $barcodeResource,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Backend\Model\UrlInterface $backendUrlBuilder,
        \Magestore\BarcodeSuccess\Model\Locator\LocatorInterface $locator,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper
    ) {
        parent::__construct($context);
        $this->_random = $random;
        $this->resource = $barcodeResource;
        $this->_localeDate = $localeDate;
        $this->_backendUrlBuilder = $backendUrlBuilder;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->locator = $locator;
        $this->storeManager = $storeManager;
        $this->escaper = $escaper;
    }

    /**
     * Generate code
     *
     * @param string $pattern
     * @return bool|string|string[]|null
     */
    public function generateCode($pattern = "")
    {
        $pattern = ($pattern) ? $pattern : $this->getStoreConfig('barcodesuccess/general/barcode_pattern');
        $pattern = strtoupper($pattern);
        $barcode = preg_replace_callback(
            '#\[([AN]{1,2})\.([0-9]+)\]#',
            [$this, 'convertExpression'],
            $pattern
        );
        $barcodeModel = $this->getModel(\Magestore\BarcodeSuccess\Api\Data\BarcodeInterface::class);
        $this->resource->load($barcodeModel, $barcode, 'barcode');
        if ($barcodeModel->getId()) {
            $count = $this->locator->get('barcode_existing_count');
            $count = ($count) ? $count++ : 1;
            $this->locator->add('barcode_existing_count', $count);
            if ($count == 5) {
                $barcode = false;
                $this->locator->remove('barcode_existing_count');
            } else {
                $barcode = $this->generateCode($pattern);
            }
        } else {
            $this->locator->remove('barcode_existing_count');
        }
        return $barcode;
    }

    /**
     * Generate barcode
     *
     * @param string $pattern
     * @return bool|string|string[]|null
     */
    public function generateBarcode($pattern = '')
    {
        $pattern = $this->getStoreConfig('barcodesuccess/general/barcode_pattern');
        $pattern = strtoupper($pattern);
        $barcode = preg_replace_callback('#\[([AN]{1,2})\.([0-9]+)\]#', [$this, 'convertExpression'], $pattern);
        $barcodeCollection = $this->_objectManager->create(
            \Magestore\BarcodeSuccess\Model\ResourceModel\Barcode\Collection::class
        );
        $barcodeCollection->addFieldToFilter('barcode', $barcode);
        $generated = $this->locator->get('generated_barcodes');
        $generated = (isset($generated)) ? $generated : [];
        if (count($barcodeCollection) > 0 || (is_array($generated) && in_array($barcode, $generated))) {
            $count = $this->locator->get('barcode_existing_count');
            $count = (isset($count)) ? $count + 1 : 1;
            $this->locator->add('barcode_existing_count', $count);
            if ($count == 5) {
                $barcode = false;
                $this->locator->remove('barcode_existing_count');
            } else {
                $barcode = $this->generateBarcode($pattern);
            }
        } else {
            $generated[] = $barcode;
            $this->locator->remove('barcode_existing_count');
            $this->locator->add('generated_barcodes', $generated);
        }
        return $barcode;
    }

    /**
     * Generate example code
     *
     * @param string $pattern
     * @return string|string[]|null
     */
    public function generateExampleCode($pattern = "")
    {
        $pattern = ($pattern) ? $pattern : $this->getStoreConfig('barcodesuccess/general/barcode_pattern');
        $barcode = preg_replace_callback(
            '#\[([AN]{1,2})\.([0-9]+)\]#',
            [$this, 'convertExpression'],
            $pattern
        );
        return $barcode;
    }

    /**
     * Convert expression
     *
     * @param array $param
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function convertExpression($param)
    {
        $alphabet = (strpos($param[1], 'A')) === false ? '' : 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $alphabet .= (strpos($param[1], 'N')) === false ? '' : '0123456789';
        return $this->_random->getRandomString($param[2], $alphabet);
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
     * Get model class
     *
     * @param string $class
     * @return mixed
     */
    public function getModel($class)
    {
        return $this->_objectManager->create($class);
    }

    /**
     * Add log
     *
     * @param string $message
     * @param string $type
     */
    public function addLog($message, $type = '')
    {
        switch ($type) {
            case 'info':
                $this->_logger->info($message);
                break;
            case 'debug':
                $this->_logger->debug($message);
                break;
            case 'notice':
                $this->_logger->notice($message);
                break;
            case 'warning':
                $this->_logger->warning($message);
                break;
            case 'error':
                $this->_logger->error($message);
                break;
            case 'emergency':
                $this->_logger->emergency($message);
                break;
            case 'critical':
                $this->_logger->critical($message);
                break;
            case 'alert':
                $this->_logger->alert($message);
                break;
            default:
                $this->_logger->error($message);
                break;
        }
    }

    /**
     * Format date
     *
     * @param string $data
     * @param string $format
     * @return mixed
     */
    public function formatDate($data, $format = '')
    {
        $format = ($format == '') ? 'M d,Y H:i:s a' : $format;
        return $this->_localeDate->date(new \DateTime($data))->format($format);
    }

    /**
     * Format price
     *
     * @param string $data
     * @return string
     */
    public function formatPrice($data)
    {
        $helper = $this->_objectManager->get(\Magento\Framework\Pricing\Helper\Data::class);
        return $helper->currency($data, true, false);
    }

    /**
     * Escape html
     *
     * @param string $str
     * @return string
     */
    public function htmlEscape($str)
    {
        return $this->escaper->escapeHtml($str);
    }

    /**
     * Get url
     *
     * @param string $path
     * @param array $params
     * @return mixed
     */
    public function getUrl($path, $params = [])
    {
        return $this->_getUrl($path, $params);
    }

    /**
     * Get backend url
     *
     * @param string $path
     * @param array $params
     * @return mixed
     */
    public function getBackendUrl($path, $params = [])
    {
        return $this->_backendUrlBuilder->getUrl($path, $params);
    }

    /**
     * Get media url
     *
     * @param string $file
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl($file)
    {
        return $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ) . $file;
    }

    /**
     * Get attribute code
     *
     * @return string
     */
    public function getAttributeCode()
    {
        $attributeCode = $this->scopeConfig->getValue(
            'barcodesuccess/general/attribute_code',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $attributeCode;
    }

    /**
     * Get barcode setup guide url
     *
     * @return string
     */
    public function getBarcodeSetupGuideUrl()
    {
        return $this->getBackendUrl('adminhtml/system_config/edit/section/barcodesuccess')
            . '#barcodesuccess_guide-link';
    }

    /**
     * Check Zend_Barcode module is installed
     *
     * @return bool
     */
    public function isZendBarcodeInstalled()
    {
        // phpcs:ignore Magento2.PHP.LiteralNamespaces.LiteralClassUsage
        return class_exists('\Zend\Barcode\Barcode');
    }
}
