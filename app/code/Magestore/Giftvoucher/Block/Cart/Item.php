<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Cart;

use Magento\Catalog\Model\Config\Source\Product\Thumbnail as ThumbnailSource;
use Magento\Checkout\Block\Cart\Item\Renderer;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;

/**
 * Giftvoucher Cart Item Block
 *
 * @module   Giftvoucher
 * @author   Magestore Developer
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Item extends Renderer implements IdentityInterface
{
    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Item constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Product\Configuration $productConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param InterpretationStrategyInterface $messageInterpretationStrategy
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     * @internal param \Magento\Catalog\Helper\Image $imageHelper
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Product\Configuration $productConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Module\Manager $moduleManager,
        InterpretationStrategyInterface $messageInterpretationStrategy,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $productConfig,
            $checkoutSession,
            $imageBuilder,
            $urlHelper,
            $messageManager,
            $priceCurrency,
            $moduleManager,
            $messageInterpretationStrategy,
            $data
        );
        $this->_objectManager = $objectManager;
        $this->setTemplate('Magestore_Giftvoucher::giftvoucher/cart/item.phtml');
    }

    /**
     * @inheritDoc
     */
    public function getProductOptions()
    {
        $giftvoucherOptions = $this->_objectManager->create(\Magestore\Giftvoucher\Helper\Data::class)
            ->getGiftVoucherOptions();
        $templates = $this->_objectManager->create(\Magestore\Giftvoucher\Model\GiftTemplate::class)
            ->getCollection()
            ->addFieldToFilter('status', '1');
        $item = parent::getItem();
        $product = $this->_objectManager->create(\Magento\Catalog\Model\Product::class)
            ->load($item->getProduct()->getId());

        $cartType = $product->getGiftCardType();

        $options = $item->getProductOptions();

        foreach ($giftvoucherOptions as $code => $label) {
            if ($option = $this->getItem()->getOptionByCode($code)) {
                if ($code == 'giftcard_template_id') {
                    foreach ($templates as $template) {
                        if ($template->getId() == $option->getValue()) {
                            $valueTemplate = $template;
                        }
                    }
                    if ($cartType !=1) {
                        $options[] = [
                            'label' => $label,
                            'value' => $this->escapeHtml($valueTemplate->getTemplateName() ?
                                $valueTemplate->getTemplateName() : $option->getValue()),
                        ];
                    }
                } elseif ($code == 'amount') {
                    $options[] = [
                        'label' => $label,
                        'value' => $this->priceCurrency->format(
                            $option->getValue(),
                            true,
                            PriceCurrencyInterface::DEFAULT_PRECISION,
                            $this->_storeManager->getStore()
                        )
                    ];
                } else {
                    $options[] = [
                        'label' => $label,
                        'value' => $this->escapeHtml($option->getValue()),
                    ];
                }
            }
        }
        return $options;
    }

    /**
     * Get Product Thumbnail
     *
     * @return string
     */
    public function getProductThumbnail()
    {
        /** @var \Magestore\Giftvoucher\Helper\Data $helper */
        $helper = $this->_objectManager->create(\Magestore\Giftvoucher\Helper\Data::class);
        return $helper->getImageUrlByQuoteItem($this->getItem());
    }

    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        $result =  $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
        $displayImageItem = $this->_objectManager->create(\Magestore\Giftvoucher\Helper\Data::class)
            ->getStoreConfig('giftvoucher/interface_checkout/display_image_item');
        if ($displayImageItem) {
            $result->setImageUrl($this->getProductThumbnail());
        }
        return $result;
    }

    /**
     * Get Image Src
     *
     * @return string
     */
    public function getImageSrc()
    {
        $thumbnail = $this->getProductThumbnail();
        return $thumbnail;
    }

    /**
     * Get Item
     *
     * @return \Magento\Quote\Model\Quote\Item\AbstractItem
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getItem()
    {
        $item = parent::getItem();

        $product = $this->_objectManager->create(\Magento\Catalog\Model\Product::class)
            ->load($item->getProduct()->getId());

        $rowTotal = $item->getRowTotal();
        $qty = $item->getQty();
        $store = $item->getStore();
        $price = $this->priceCurrency->round($rowTotal) / $qty;

        $baseCurrencyCode = $this->_storeManager->getStore()->getBaseCurrencyCode();
        $quoteCurrencyCode = $item->getQuote()->getQuoteCurrencyCode();
        $baseCurrency = $this->_objectManager->create(\Magento\Directory\Model\Currency::class)
            ->load($baseCurrencyCode);

        if ($baseCurrencyCode != $quoteCurrencyCode) {
            $quoteCurrency = $this->_objectManager->create(\Magento\Directory\Model\Currency::class)
                ->load($quoteCurrencyCode);
            if ($product->getGiftType() == \Magestore\Giftvoucher\Model\Source\GiftType::GIFT_TYPE_RANGE) {
                $price = $price * $price / $baseCurrency->convert($price, $quoteCurrency);
                $item->setPrice($price);
            }
        }

        $options = $item->getOptions();
        $result = [];
        foreach ($options as $option) {
            $result[$option->getCode()] = $option->getValue();
        }

        if (isset($result['base_gc_value']) && isset($result['base_gc_currency'])) {
            $currency = $store->getCurrentCurrencyCode();
            $currentCurrency = $this->_objectManager->create(\Magento\Directory\Model\Currency::class)->load($currency);
            $amount = $baseCurrency->convert($result['base_gc_value'], $currentCurrency);
            foreach ($options as $option) {
                if ($option->getCode() == 'amount') {
                    $option->setValue($amount);
                }
            }
            $item->setOptions($options)->save();
        }

        return $item;
    }
}
