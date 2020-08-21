<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Adminhtml\Product;

use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Adminhtml Giftvoucher Product View Block
 *
 * @author      Magestore Developer
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends \Magento\Catalog\Block\Product\View\AbstractView
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Catalog product
     *
     * @var \Magento\Catalog\Helper\Product
     */
    protected $catalogProduct;

    /**
     * @var \Magento\Bundle\Model\Product\PriceFactory
     */
    protected $productPriceFactory;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $_localeFormat;

    /**
     * Giftproduct data
     *
     * @var \Magento\Bundle\Helper\Giftproduct
     */
    protected $_giftproductData = null;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogHelper;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * Giftvoucher data
     *
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $_giftvoucherData = null;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Request
     *
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_dataObject;

    protected $timezone;

    /**
     * View constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magestore\Giftvoucher\Helper\Giftproduct $helperData
     * @param \Magestore\Giftvoucher\Helper\Data $giftvoucherData
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\Giftvoucher\Model\Product\PriceFactory $productPrice
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\DataObject $dataObject
     * @param \Magento\Config\Model\Config\Source\Locale\Timezone $timezone
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magestore\Giftvoucher\Helper\Giftproduct $helperData,
        \Magestore\Giftvoucher\Helper\Data $giftvoucherData,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Giftvoucher\Model\Product\PriceFactory $productPrice,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\DataObject $dataObject,
        \Magento\Config\Model\Config\Source\Locale\Timezone $timezone,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->_giftvoucherData = $giftvoucherData;
        $this->priceCurrency = $priceCurrency;
        $this->_catalogHelper = $context->getCatalogHelper();
        $this->_giftproductData = $helperData;
        $this->catalogProduct = $catalogProduct;
        $this->productPriceFactory = $productPrice;
        $this->jsonEncoder = $jsonEncoder;
        $this->_dataObject = $dataObject;
        $this->timezone = $timezone;
        parent::__construct(
            $context,
            $arrayUtils,
            $data
        );
    }

    /**
     * Get Locale Format
     *
     * @return \Magento\Framework\Locale\FormatInterface
     */
    public function getLocaleFormat()
    {
        return $this->_localeFormat;
    }

    /**
     * Get Tax Rate
     *
     * @return float|int
     */
    public function getTaxRate()
    {
        $product = $this->getProduct();
        $includeTax = (
            $this->getTaxHelper()->getPriceDisplayType() != \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX
        );
        $rateTax = $this->getCatalogHelper()->getTaxPrice($product, 100, $includeTax) / 100;
        return $rateTax;
    }

    /**
     * Get Recipient Ship Desc
     *
     * @return \Magento\Framework\Phrase
     */
    public function getRecipientShipDesc()
    {
        if ($this->getGiftvoucherHelper()->getInterfaceConfig('postoffice_date')) {
            return __(
                "You need to fill in your friend's address as the shipping address on checkout page. "
                . "We will send this Gift Card to that address after at least %1 day(s).",
                $this->getGiftvoucherHelper()->getInterfaceConfig('postoffice_date')
            );
        } else {
            return __(
                "You need to fill in your friend's address as the shipping address on checkout page. "
                . "We will send this Gift Card to that address."
            );
        }
    }

    /**
     * Get Time Zones
     *
     * @return array
     */
    public function getTimeZones()
    {
        return $this->timezone->toOptionArray();
    }

    /**
     * Get the price information of Gift Card product
     *
     * @param \Magestore\Giftvoucher\Model\Product $product
     * @return array
     */
    public function getGiftAmount($product)
    {
        $giftValue = $this->_giftproductData->getGiftValue($product);
        switch ($giftValue['type']) {
            case 'range':
                $giftValue['from'] = $this->convertPrice($product, $giftValue['from']);
                $giftValue['to'] = $this->convertPrice($product, $giftValue['to']);
                $giftValue['from_txt'] = $this->priceCurrency->format($giftValue['from']);
                $giftValue['to_txt'] = $this->priceCurrency->format($giftValue['to']);
                break;
            case 'dropdown':
                $giftValue['options'] = $this->_convertPrices($product, $giftValue['options']);
                $giftValue['prices'] = $this->_convertPrices($product, $giftValue['prices']);
                $giftValue['prices'] = array_combine($giftValue['options'], $giftValue['prices']);
                $giftValue['options_txt'] = $this->_formatPrices($giftValue['options']);
                break;
            case 'static':
                $giftValue['value'] = $this->convertPrice($product, $giftValue['value']);
                $giftValue['value_txt'] = $this->priceCurrency->format($giftValue['value']);
                $giftValue['price'] = $this->convertPrice($product, $giftValue['gift_price']);
                break;
            default:
                $giftValue['type'] = 'any';
        }
        return $giftValue;
    }

    /**
     * Convert Gift Card base prices
     *
     * @param \Magestore\Giftvoucher\Model\Product $product
     * @param array $basePrices
     * @return array
     */
    protected function _convertPrices($product, $basePrices)
    {
        foreach ($basePrices as $key => $price) {
            $basePrices[$key] = $this->convertPrice($product, $price);
        }
        return $basePrices;
    }

    /**
     * Get Gift Card product price with all tax settings processing
     *
     * @param \Magestore\Giftvoucher\Model\Product $product
     * @param float $price
     * @return float
     */
    public function convertPrice($product, $price)
    {
        $includeTax = ( $this->_taxData->getPriceDisplayType() != 1 );

        $priceWithTax = $this->_catalogHelper->getTaxPrice($product, $price, $includeTax);
        return $this->priceCurrency->convert($priceWithTax);
    }

    /**
     * Formatted Gift Card price
     *
     * @param array $prices
     * @return array
     */
    protected function _formatPrices($prices)
    {
        foreach ($prices as $key => $price) {
            $prices[$key] = $this->priceCurrency->format($price);
        }
        return $prices;
    }

    /**
     * Message Max Len
     *
     * @return int
     */
    public function messageMaxLen()
    {
        return (int) $this->_giftvoucherData->getInterfaceConfig('max');
    }

    /**
     * Enable Physical Mail
     *
     * @return bool
     */
    public function enablePhysicalMail()
    {
        return $this->_giftvoucherData->getInterfaceConfig('postoffice');
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
        $formData = [];
        if ($action == 'checkout_cart_configure' && $request->getParam('id')) {
            $options = $this->_objectManager->create(\Magento\Quote\Model\Quote\Item\Option::class)
                ->getCollection()
                ->addItemFilter($request->getParam('id'));

            foreach ($options as $option) {
                $formData[$option->getCode()] = $option->getValue();
            }
        }
        $dataObject = $this->_dataObject->setData($formData);
        return $dataObject;
    }

    /**
     * Enable Schedule Send
     *
     * @return bool
     */
    public function enableScheduleSend()
    {
        return $this->_giftvoucherData->getInterfaceConfig('schedule');
    }

    /**
     * Get Gift Amount Description
     *
     * @return mixed
     */
    public function getGiftAmountDescription()
    {
        if (!$this->hasData('gift_amount_description')) {
            $product = $this->getProduct();
            $this->setData('gift_amount_description', '');
            if ($product->getShowGiftAmountDesc()) {
                if ($product->getGiftAmountDesc()) {
                    $this->setData('gift_amount_description', $product->getGiftAmountDesc());
                } else {
                    $this->setData(
                        'gift_amount_description',
                        $this->_giftvoucherData->getInterfaceConfig('description')
                    );
                }
            }
        }
        return $this->getData('gift_amount_description');
    }

    /**
     * Get Available Template
     *
     * @return mixed
     */
    public function getAvailableTemplate()
    {
        $templates = $this->_objectManager->create(\Magestore\Giftvoucher\Model\GiftTemplate::class)->getCollection()
                ->addFieldToFilter('status', '1');
        return $templates;
    }

    /**
     * Get Available Template Admin
     *
     * @return mixed
     */
    public function getAvailableTemplateAdmin()
    {
        $product = $this->getProduct();
        $productTemplate = $product->getGiftTemplateIds();
        if ($productTemplate) {
            $productTemplate = explode(',', $productTemplate);
        } else {
            $productTemplate = [];
        }

        $templates = $this->_objectManager->create(\Magestore\Giftvoucher\Model\GiftTemplate::class)->getCollection()
                ->addFieldToFilter('status', '1')
                ->addFieldToFilter('giftcard_template_id', ['in' => $productTemplate]);

        return $templates->getData();
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
     * Retrieve product
     *
     * @return Product|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', $this->_coreRegistry->registry('current_product'));
        }
        $product = $this->getData('product');
        if ($product->getTypeInstance(true)->getStoreFilter($product) === null) {
            $product->getTypeInstance(true)
                ->setStoreFilter($this->_storeManager->getStore($product->getStoreId()), $product);
        }
        $this->_product = $this->_coreRegistry->registry('giftvoucher_product_' . $product->getId());
        return $product;
    }

    /**
     * Get Gift Card product options
     *
     * @param mixed $val
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOptionProduct($val)
    {
        if (!$this->_product) {
            $this->getProduct();
        }
        if ($this->_product) {
            $option = $this->_product->getCustomOptions();
            if ($option && isset($option[$val]) && $option[$val]) {
                return $option[$val]->getValue();
            }
        }
        return '';
    }

    /**
     * Get Allow Attributes
     *
     * @return array
     */
    public function getAllowAttributes()
    {
        return $this->_giftvoucherData->getFullGiftVoucherOptions();
    }

    /**
     * Get Giftvoucher Helper
     *
     * @return \Magestore\Giftvoucher\Helper\Data
     */
    public function getGiftvoucherHelper()
    {
        return $this->_giftvoucherData;
    }

    /**
     * Get Request Interface
     *
     * @return \Magento\Framework\App\RequestInterface
     */
    public function getRequestInterface()
    {
        return $this->_request;
    }

    /**
     * Get Store Manager
     *
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }

    /**
     * Get Json Encode
     *
     * @return \Magento\Framework\Json\EncoderInterface
     */
    public function getJsonEncode()
    {
        return $this->jsonEncoder;
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
