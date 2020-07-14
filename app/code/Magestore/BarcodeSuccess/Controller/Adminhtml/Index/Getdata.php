<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magestore\BarcodeSuccess\Helper\Data;
use \Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magestore\BarcodeSuccess\Model\Locator\LocatorInterface;

/**
 * Class Getdata
 * @package Magestore\BarcodeSuccess\Controller\Adminhtml\Index
 */
class Getdata extends \Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractIndex
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var ImageHelper
     */
    protected $imageHelper;

    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * Getdata constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param ImageHelper $imageHelper
     * @param StockRegistryInterface $stockRegistry
     * @param Data $data
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        ImageHelper $imageHelper,
        StockRegistryInterface $stockRegistry,
        Data $data,
        LocatorInterface $locator
    ) {
        parent::__construct($context, $resultPageFactory, $data, $locator);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->imageHelper = $imageHelper;
        $this->stockRegistry = $stockRegistry;
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $barcode = $this->getRequest()->getParam('barcode');
        if(empty($barcode)){
            $resultJson->setData([
                'messages' => __('Please scan your barcode to get data'),
                'error' => true
            ]);
        }else{
            $data = [];
            $barcodeModel = $this->helper->getModel('\Magestore\BarcodeSuccess\Api\Data\BarcodeInterface');
            $this->helper->resource->load($barcodeModel, $barcode, 'barcode');
            if ($barcodeModel->getId()) {
                $data = $barcodeModel->getData();
                if(isset($data['product_id'])){
                    $product = $this->helper->getModel('\Magento\Catalog\Api\Data\ProductInterface');
                    $productResource = $this->helper->getModel('\Magento\Catalog\Model\ResourceModel\Product');
                    $productResource->load($product, $data['product_id']);
                    if($product->getId()){
                        $id = $product->getId();
                        $stockAvailability = $this->stockRegistry->getStockStatus($id)->getStockStatus();
                        $origImageHelper = $this->imageHelper->init($product, 'product_listing_thumbnail_preview');
                        $data['product_image'] = $origImageHelper->getUrl();
                        $data['product_name'] = $product->getName();
                        $data['product_price'] = $this->helper->formatPrice($product->getPrice());
                        $data['product_weight'] = $product->getWeight();
                        $data['product_color'] = $product->getColor();
                        $data['product_stock'] = ($stockAvailability)?__('In Stock'):__('Out of Stock');
                        $data['product_status'] = ($product->getStatus())?__('Enabled'):__('Disabled');
                        $data['more_detail_url'] = $this->getUrl(
                            'catalog/product/edit',
                            ['id' => $id, 'store' => $product->getStore()->getId()]
                        );
                    }
                }
                if(isset($data['created_at'])){
                    $data['created_at'] = $this->helper->formatDate($data['created_at']);
                }
                if(isset($data['purchased_time'])){
                    $data['purchased_time'] = $this->helper->formatDate($data['purchased_time']);
                }
                $resultJson->setData([
                    'data' => $data,
                    'success' => true
                ]);
            }else{
                $resultJson->setData([
                    'messages' => __('Cannot find any records for %1', $barcode),
                    'error' => true
                ]);
            }
        }

        return $resultJson;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_BarcodeSuccess::scan_barcode');
    }
}
