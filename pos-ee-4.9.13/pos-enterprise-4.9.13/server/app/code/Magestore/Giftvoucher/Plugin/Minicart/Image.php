<?php

namespace Magestore\Giftvoucher\Plugin\Minicart;
use Magento\Framework\App\ObjectManager;

/**
 * Class Image
 * @package Magestore\Giftvoucher\Plugin\Minicart
 */
class Image
{
    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $helperData;

    /**
     * Image constructor.
     * @param \Magestore\Giftvoucher\Helper\Data $helperData
     */
    public function __construct(
        \Magestore\Giftvoucher\Helper\Data $helperData
    )
    {
        $this->helperData = $helperData;
    }

    /**
     * @param $subject
     * @param $proceed
     * @param $item
     * @return mixed
     */
    public function aroundGetItemData($subject, $proceed, $item)
    {
        $result = $proceed($item);
        if ($this->helperData->getStoreConfig('giftvoucher/interface_checkout/display_image_item')
            && $item->getProduct()->getTypeId() == \Magestore\Giftvoucher\Model\Product\Type\Giftvoucher::GIFT_CARD_TYPE){
            /** @var \Magestore\Giftvoucher\Helper\Data $helper */
            $helper = ObjectManager::getInstance()->create('Magestore\Giftvoucher\Helper\Data');
            $result['product_image']['src'] =  $helper->getImageUrlByQuoteItem($item);
        }
        return $result;
    }
}