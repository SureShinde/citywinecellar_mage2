<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

namespace Magestore\Customercredit\Block\Adminhtml\Product;

use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class View
 *
 * Product view block
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends \Magento\Catalog\Block\Product\View\AbstractView
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var \Magestore\Customercredit\Helper\Data
     */
    protected $_customercreditData;
    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogHelper;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_helperData;
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;
    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $_localeFormat;
    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $_sessionQuote;
    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxData;
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * View constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param \Magestore\Customercredit\Helper\Creditproduct $helperData
     * @param \Magestore\Customercredit\Helper\Data $customercreditData
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magestore\Customercredit\Helper\Creditproduct $helperData,
        \Magestore\Customercredit\Helper\Data $customercreditData,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->_request = $context->getRequest();
        $this->_objectManager = $objectManager;
        $this->_customercreditData = $customercreditData;
        $this->_catalogHelper = $context->getCatalogHelper();
        $this->_storeManager = $context->getStoreManager();
        $this->_helperData = $helperData;
        $this->jsonEncoder = $jsonEncoder;
        $this->_localeFormat = $localeFormat;
        $this->_sessionQuote = $sessionQuote;
        $this->_taxData = $context->getTaxData();
        $this->priceCurrency = $priceCurrency;
        parent::__construct(
            $context,
            $arrayUtils,
            $data
        );
    }

    /**
     * Get Helper Data
     *
     * @return \Magestore\Customercredit\Helper\Data
     */
    public function getHelperData()
    {
        return $this->_customercreditData;
    }

    /**
     * Get Credit Amount
     *
     * @param Product $product
     * @return array
     */
    public function getCreditAmount($product)
    {
        $creditValue = $this->_helperData->getCreditDataByProduct($product);
        switch ($creditValue['type']) {
            case 'range':
                $creditValue['from'] = $this->convertPrice($product, $creditValue['from']);
                $creditValue['to'] = $this->convertPrice($product, $creditValue['to']);
                $creditValue['from_txt'] = $this->priceCurrency->format($creditValue['from']);
                $creditValue['to_txt'] = $this->priceCurrency->format($creditValue['to']);
                break;
            case 'dropdown':
                $creditValue['options'] = $this->_convertPrices($product, $creditValue['options']);
                $creditValue['prices'] = $this->_convertPrices($product, $creditValue['prices']);
                $creditValue['prices'] = array_combine($creditValue['options'], $creditValue['prices']);
                $creditValue['options_txt'] = $this->_formatPrices($creditValue['options']);
                break;
            case 'static':
                $creditValue['value'] = $this->convertPrice($product, $creditValue['value']);
                $creditValue['value_txt'] = $this->priceCurrency->format($creditValue['value']);
                $creditValue['price'] = $this->convertPrice($product, $creditValue['credit_price']);
                break;
            default:
                $creditValue['type'] = 'any';
        }

        return $creditValue;
    }

    /**
     * Convert Prices
     *
     * @param Product $product
     * @param array $basePrices
     * @return array
     */
    public function _convertPrices($product, $basePrices)
    {
        foreach ($basePrices as $key => $price) {
            $basePrices[$key] = $this->convertPrice($product, $price);
        }
        return $basePrices;
    }

    /**
     * Convert Price
     *
     * @param Product $product
     * @param float $price
     * @return float
     */
    public function convertPrice($product, $price)
    {
        $includeTax = ($this->_taxData->getPriceDisplayType() != 1);
        $priceWithTax = $this->_catalogHelper->getTaxPrice($product, $price, $includeTax);
        return $this->priceCurrency->convert($priceWithTax);
    }

    /**
     * Format prices
     *
     * @param array $prices
     * @return array
     */
    public function _formatPrices($prices)
    {
        foreach ($prices as $key => $price) {
            $prices[$key] = $this->priceCurrency->format($price, false);
        }
        return $prices;
    }

    /**
     * Get Form Config Data
     *
     * @return \Magento\Framework\DataObject
     */
    public function getFormConfigData()
    {
        $request = $this->_request;
        $action = $request->getFullActionName();
        if (($action == 'checkout_cart_configure' || $action == 'sales_order_create_configureQuoteItems')
            && $request->getParam('id')
        ) {
            $options = $this->_objectManager->create(\Magento\Quote\Model\Quote\Item\Option::class)
                ->getCollection()
                ->addItemFilter($request->getParam('id'));
            $formData = [];
            foreach ($options as $option) {
                $formData[$option->getCode()] = $option->getValue();
            }
            return new \Magento\Framework\DataObject($formData);
        }
        return new \Magento\Framework\DataObject();
    }

    /**
     * Get Price Format Js
     *
     * @return string
     */
    public function getPriceFormatJs()
    {
        $priceFormat = $this->_localeFormat->getPriceFormat();
        return $this->jsonEncoder->encode($priceFormat);
    }

    /**
     * Get Tax Helper
     *
     * @return \Magento\Tax\Helper\Data
     */
    public function getTaxHelper()
    {
        return $this->_taxData;
    }

    /**
     * Get Catalog Helper
     *
     * @return \Magento\Catalog\Helper\Data
     */
    public function getCatalogHelper()
    {
        return $this->_catalogHelper;
    }

    /**
     * Get Object Manager
     *
     * @return \Magento\Framework\ObjectManagerInterface
     */
    public function getObjectManager()
    {
        return $this->_objectManager;
    }
}
