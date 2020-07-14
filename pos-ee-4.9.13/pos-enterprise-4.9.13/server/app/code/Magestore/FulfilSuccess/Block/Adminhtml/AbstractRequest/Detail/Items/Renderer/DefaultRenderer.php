<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail\Items\Renderer;

use Magento\Sales\Model\Order\Item;
use Magestore\FulfilSuccess\Api\Data\PickRequestItemInterface;
use Magestore\FulfilSuccess\Api\Data\PackRequestInterface;

class DefaultRenderer extends \Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer
{
    protected $imageHelper;

    /**
     * @var \Magento\Shipping\Model\CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * DefaultRenderer constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\GiftMessage\Helper\Message $messageHelper
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\GiftMessage\Helper\Message $messageHelper,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory,
        array $data = [])
    {
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $messageHelper, $checkoutHelper, $data);
        $this->imageHelper = $imageHelper;
        $this->_carrierFactory = $carrierFactory;
    }

    /**
     * @param \Magento\Framework\DataObject|Item $item
     * @param string $column
     * @param null $field
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getColumnHtml(\Magento\Framework\DataObject $item, $column, $field = null)
    {
        $html = '';
        switch ($column) {
            case 'product':
                if ($this->canDisplayContainer()) {
                    $html .= '<div id="' . $this->getHtmlId() . '">';
                }
                $html .= $this->getColumnHtml($item, 'name');
                if ($this->canDisplayContainer()) {
                    $html .= '</div>';
                }
                break;
            case 'image':
                $html = $this->displayProductImage($item);
                break;
            case 'barcode':
                $html = $this->displayProductBarcode($item);
                break;
            case 'qty':
                $html = $this->displayQty($item);
                break;
            case 'request_qty':
                $html = $this->displayRequestQty($item);
                break;
            case 'picked_qty':
                $html = $this->displayPickedQty($item);
                break;
            case 'status':
                $html = $item->getStatus();
                break;
            case 'price-original':
                $html = $this->displayPriceAttribute('original_price');
                break;
            case 'tax-amount':
                $html = $this->displayPriceAttribute('tax_amount');
                break;
            case 'tax-percent':
                $html = $this->displayTaxPercent($item);
                break;
            case 'discont':
                $html = $this->displayPriceAttribute('discount_amount');
                break;
            default:
                $html = parent::getColumnHtml($item, $column, $field);
        }
        return $html;
    }

    /**
     * @param \Magento\Framework\DataObject $item
     */
    public function displayProductImage(\Magento\Framework\DataObject $item)
    {
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        $product = $item->getProduct();
        $imageUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product/' . $product->getData('small_image');
        return '<img src="' . $imageUrl . '" class="thumbnail_product_image"/>';
    }

    /**
     * @param \Magento\Framework\DataObject $item
     * @return string
     */
    public function displayProductBarcode(\Magento\Framework\DataObject $item)
    {
        $barcodes = $item->getData(PickRequestItemInterface::ITEM_BARCODE);
        $barcodes = explode('||',$barcodes);
        $barcodes = implode(', ',$barcodes);
        return $barcodes;
    }

    /**
     * @param \Magento\Framework\DataObject $item
     * @return string
     */
    public function displayRequestQty(\Magento\Framework\DataObject $item)
    {
        return '<input type="hidden" id="shipment[items][' . $item->getId() . ']" name="shipment[items][' . $item->getId() . ']" value="' . $item->getRequestQty() . '"/><span>' . floatval($item->getRequestQty()) . '</span>';
    }

    /**
     * @param \Magento\Framework\DataObject $item
     * @return string
     */
    public function displayPickedQty(\Magento\Framework\DataObject $item)
    {
        /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
        if ($item->getParentItemId()) {
            $itemId = $item->getParentItemId();
        } else {
            $itemId = $item->getItemId();
        }
        /**Michael**/
        if ($this->checkCarrierAvailable()) {
            return '<input type="hidden" id="shipment[items][' . $itemId . ']" name="shipment[items][' . $itemId . ']" value="' . $item->getRequestQty() . '"/><span>' . intval($item->getRequestQty()) . '</span>';
        } else {
            return '<input type="number" class="input-text admin__control-text qty-item qty-to-ship-input" min="0" max="'.floatval($item->getRequestQty() - $item->getPackedQty()).'" id="shipment[items][' . $itemId . ']" name="shipment[items][' . $itemId . ']" value="' . floatval($item->getRequestQty() - $item->getPackedQty()) . '"/>';
        }
    }

    /**
     * @param \Magento\Framework\DataObject $item
     * @return string
     */
    public function displayQty(\Magento\Framework\DataObject $item)
    {
        $html = '<span>' . __('Request Qty: ').intval($item->getRequestQty()) . '</span>';
        if ($item->getPackedQty()) {
            $html .= '<br /><span>' . __('Packed Qty: ').intval($item->getPackedQty()) . '</span>';
        }
        if ($this->isPackedRequest() && ($item->getRequestQty() - $item->getPackedQty()) > 0) {
            $html .= '<br /><span>' . __('Qty moved to Pick / Prepare-Fulfil') . ': ' .intval($item->getRequestQty() - $item->getPackedQty()) . '</span>';
        }
        return $html;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkCarrierAvailable()
    {
        $shippingCarrier = $this->_carrierFactory->create(
            $this->getOrder()->getShippingMethod(true)->getCarrierCode()
        );
        if ($shippingCarrier && $shippingCarrier->isShippingLabelsAvailable())
            return true;
        return false;
    }

    /**
     * @return bool
     */
    public function isPackedRequest()
    {
        $packRequest = $this->_coreRegistry->registry('current_pack_request');
        if ($packRequest && $packRequest->getId()) {
            if (in_array($packRequest->getData(PackRequestInterface::STATUS),
                [PackRequestInterface::STATUS_PACKED, PackRequestInterface::STATUS_CANCELED])) {
                return true;
            }
//            return ($packRequest->getData(PackRequestInterface::STATUS) == PackRequestInterface::STATUS_PACKED) ? true : false;
        }
        return false;
    }
}