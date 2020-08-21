<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Helper;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magestore\Giftvoucher\Model\Product\Type\Giftvoucher;
use Magestore\Giftvoucher\Model\Source\GiftPriceType;

/**
 * Giftvoucher product helper
 *
 * @module   Giftvoucher
 * @author   Magestore Developer
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Giftproduct extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Set
     */
    protected $_attributeSet;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $_inlineTranslation;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currencyModel;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_locale;

    /**
     * Giftvoucher data
     *
     * @var \Magento\Bundle\Helper\Giftvoucher
     */
    protected $_giftvoucherData = null;

    /**
     * Giftproduct constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param Data $helperData
     * @param \Magento\Eav\Model\Entity\Attribute\Set $attributeSet
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\Currency $currencyModel
     * @param \Magento\Framework\Locale\ResolverInterface $locale
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        PriceCurrencyInterface $priceCurrency,
        \Magestore\Giftvoucher\Helper\Data $helperData,
        \Magento\Eav\Model\Entity\Attribute\Set $attributeSet,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\Currency $currencyModel,
        \Magento\Framework\Locale\ResolverInterface $locale
    ) {
        $this->_giftvoucherData = $helperData;
        $this->_objectManager = $objectManager;
        $this->_priceCurrency = $priceCurrency;
        $this->_productFactory = $productFactory;
        $this->_product = $product;
        $this->_customer = $customer;
        $this->_attributeSet = $attributeSet;
        $this->_transportBuilder = $transportBuilder;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_storeManager = $storeManager;
        $this->_currencyModel = $currencyModel;
        $this->_locale = $locale;
        parent::__construct($context);
    }

    /**
     * Get the price information of Gift Card product
     *
     * @param \Magestore\Giftvoucher\Model\Product $product
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getGiftValue($product)
    {
        if (!$product->getGiftType() && $product->getTypeId() == Giftvoucher::GIFT_CARD_TYPE) {
            /** @var \Magento\Catalog\Model\Product $productModel */
            $productModel = $this->_objectManager->create(\Magento\Catalog\Model\Product::class);
            $productModel->load($product->getId());
            $product = $productModel;
        }
        switch ($product->getGiftType()) {
            case \Magestore\Giftvoucher\Model\Source\GiftType::GIFT_TYPE_FIX:
                return [
                    'type' => 'static',
                    'gift_price' => $this->getGiftPriceByStatic($product),
                    'value' => $product->getGiftValue()
                ];

            case \Magestore\Giftvoucher\Model\Source\GiftType::GIFT_TYPE_RANGE:
                $data = ['type' => 'range', 'from' => $product->getGiftFrom(), 'to' => $product->getGiftTo()];
                $priceType = $product->getGiftPriceType();

                if ($priceType == \Magestore\Giftvoucher\Model\Source\GiftPriceType::GIFT_PRICE_TYPE_DEFAULT) {
                    $data['gift_price_type'] = 'default';
                } elseif ($priceType == \Magestore\Giftvoucher\Model\Source\GiftPriceType::GIFT_PRICE_TYPE_FIX) {
                    $data['gift_price_type'] = 'fixed';
                    $data['gift_price'] = $product->getGiftPrice();
                } else {
                    $data['gift_price_type'] = 'percent';
                    $data['gift_price_options'] = $product->getGiftPrice();
                }
                return $data;

            case \Magestore\Giftvoucher\Model\Source\GiftType::GIFT_TYPE_DROPDOWN:
                $options = explode(',', $product->getGiftDropdown());
                $giftPrices = explode(',', $product->getGiftPrice());

                foreach ($options as $key => $option) {
                    if (!is_numeric($option) || $option <= 0) {
                        unset($options[$key]);
                    }
                }

                $data = ['type' => 'dropdown', 'options' => $options];
                $priceType = $product->getGiftPriceType();
                if ($priceType == \Magestore\Giftvoucher\Model\Source\GiftPriceType::GIFT_PRICE_TYPE_DEFAULT) {
                    $data['prices'] = $options;
                } elseif ($priceType == \Magestore\Giftvoucher\Model\Source\GiftPriceType::GIFT_PRICE_TYPE_FIX) {
                    $optionsPrice = explode(',', $product->getGiftPrice());
                    $data['prices'] = $optionsPrice;
                } else {
                    if (count($giftPrices) == count($options)) {
                        $giftPricesLength = count($giftPrices);
                        for ($i = 0; $i < $giftPricesLength; $i++) {
                            $data['prices'][] = $giftPrices[$i] * $options[$i] / 100;
                        }
                    } else {
                        foreach ($options as $value) {
                            $data['prices'][] = $value * $product->getGiftPrice() / 100;
                        }
                    }
                }

                return $data;
            default:
                $giftValue = $this->_giftvoucherData->getInterfaceConfig('amount');
                $options = explode(',', $giftValue);
                return ['type' => 'dropdown', 'options' => $options, 'prices' => $options];
        }
    }

    /**
     * Get the static price of Gift Card product
     *
     * @param \Magestore\Giftvoucher\Model\Product $product
     * @return float
     */
    public function getGiftPriceByStatic($product)
    {
        $giftValue = $product->getGiftValue();
        $giftPrice = $product->getGiftPrice();
        if ($product->getGiftPriceType() == GiftPriceType::GIFT_PRICE_TYPE_DEFAULT) {
            return $giftValue;
        } elseif ($product->getGiftPriceType() == GiftPriceType::GIFT_PRICE_TYPE_FIX) {
            return $giftPrice;
        } else {
            return $giftValue * $giftPrice / 100;
        }
    }
}
