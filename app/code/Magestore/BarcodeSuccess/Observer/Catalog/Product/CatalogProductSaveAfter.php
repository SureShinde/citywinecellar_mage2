<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Observer\Catalog\Product;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magestore\BarcodeSuccess\Model\History;

/**
 * Class \Magestore\BarcodeSuccess\Observer\Catalog\Product\CatalogProductSaveAfter
 */
class CatalogProductSaveAfter implements ObserverInterface
{
    /**
     * @var \Magestore\BarcodeSuccess\Model\BarcodeFactory
     */
    protected $barcodeFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magestore\BarcodeSuccess\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magestore\BarcodeSuccess\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magestore\BarcodeSuccess\Helper\Attribute
     */
    protected $attributeHelper;

    /**
     * CatalogProductSaveAfter constructor.
     *
     * @param \Magestore\BarcodeSuccess\Model\BarcodeFactory $barcodeFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magestore\BarcodeSuccess\Helper\Data $helper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magestore\BarcodeSuccess\Model\ProductFactory $productFactory
     * @param \Magestore\BarcodeSuccess\Helper\Attribute $attributeHelper
     */
    public function __construct(
        \Magestore\BarcodeSuccess\Model\BarcodeFactory $barcodeFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magestore\BarcodeSuccess\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magestore\BarcodeSuccess\Model\ProductFactory $productFactory,
        \Magestore\BarcodeSuccess\Helper\Attribute $attributeHelper
    ) {
        $this->barcodeFactory = $barcodeFactory;
        $this->request = $request;
        $this->helper = $helper;
        $this->messageManager = $messageManager;
        $this->productFactory = $productFactory;
        $this->attributeHelper = $attributeHelper;
    }

    /**
     * Execute observer
     *
     * @param EventObserver $observer
     * @return \Magento\Framework\Message\ManagerInterface|void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        $barcode = $this->request->getParam('os_barcode');
        $product = $observer->getData('product');
        $barcodeModel = $this->barcodeFactory->create();
        $attributeCode = $this->helper->getAttributeCode();
        if ($attributeCode) {
            $barcodeId = false;
            $productId = $product->getId();
            $productBarcodeModel = $this->productFactory->create()->load($productId);

            $barcodeAttribute = $productBarcodeModel->getResource()->getAttribute($attributeCode);
            if ($barcodeAttribute) {
                $attributeValue = $barcodeAttribute->getFrontend()->getValue($productBarcodeModel);
                $barcodeId = $barcodeModel->getCollection()
                    ->addFieldToFilter('barcode', $attributeValue)
                    ->addFieldToFilter('product_id', ['eq' => $product->getId()])
                    ->setPageSize(1)
                    ->setCurPage(1)
                    ->getFirstItem()
                    ->getId();
            }

            if (!$barcodeId) {
                // generate barcode for product if it does NOT exist
                $historyId = $this->attributeHelper->saveHistory(
                    1,
                    History::GENERATED,
                    __('Import from attribute')
                );
                $productBarcodeModel->migrateBarcode($attributeCode, $historyId);
            }
        }
        if (isset($barcode) && !empty($barcode)) {
            $product = $observer->getData('product');
            $barcodeModel = $this->barcodeFactory->create();
            $barcodeId = $barcodeModel->getCollection()
                ->addFieldToFilter('barcode', $barcode)
                ->addFieldToFilter('product_id', ['neq' => $product->getId()])
                ->setPageSize(1)
                ->setCurPage(1)
                ->getFirstItem()
                ->getId();
            if ($barcodeId) {
                return $this;//$this->messageManager->addErrorMessage(__('Barcode has been existed.'));
            }
            $barcodeModel->getResource()->load($barcodeModel, $product->getId(), 'product_id');
            if ((string)$barcode !== (string)$barcodeModel->getBarcode()) {
                if ($barcodeModel->getId()) {
                    $barcodeModel->setBarcode($barcode);
                } else {
                    $historyId = $this->saveHistory();
                    $barcodeData = [
                        'product_id' => $product->getId(),
                        'barcode' => $barcode,
                        'qty' => 1,
                        'product_sku' => $product->getSku(),
                        'supplier_code' => '',
                        'history_id' => $historyId
                    ];
                    $barcodeModel->addData($barcodeData);
                }

                try {
                    $barcodeModel->getResource()->save($barcodeModel);
                } catch (\Exception $e) {
                    $this->helper->addLog($e->getMessage());
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }
        }
    }

    /**
     * Save history
     *
     * @return string
     */
    public function saveHistory()
    {
        $historyId = '';
        $history = $this->helper->getModel(\Magestore\BarcodeSuccess\Api\Data\HistoryInterface::class);
        $historyResource = $this->helper->getModel(\Magestore\BarcodeSuccess\Model\ResourceModel\History::class);
        $adminSession = $this->helper->getModel(\Magento\Backend\Model\Auth\Session::class);
        try {
            $admin = $adminSession->getUser();
            $adminId = ($admin) ? $admin->getId() : 0;
            $history->setData('type', \Magestore\BarcodeSuccess\Model\History::GENERATED);
            $history->setData('reason', __('Create barcode for product'));
            $history->setData('created_by', $adminId);
            $history->setData('total_qty', 1);
            $historyResource->save($history);
            $historyId = $history->getId();
        } catch (\Exception $e) {
            $this->helper->addLog($e->getMessage());
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $historyId;
    }
}
