<?php

namespace Magestore\PurchaseOrderSuccess\Controller\PurchaseOrder;

use Magento\Framework\App\Filesystem\DirectoryList;

class Downloadcsv extends \Magestore\PurchaseOrderSuccess\Controller\AbstractController {
    protected $purchaseOrder;

    public function execute() {
        $purchaseKey = $this->getRequest()->getParam('key');
        $purchaseOrder = $this->purchaseOrderRepository->getByKey($purchaseKey);
        $this->purchaseOrder = $purchaseOrder;
        if($purchaseOrder && $purchaseOrder->getPurchaseOrderId()){
            $filenameDownload = 'items-of-purchase-order-'.$purchaseOrder->getPurchaseCode().'.csv';
            $this->getBaseDirMedia()->create('magestore/purchaseOrder/detail');
            $filename = $this->getBaseDirMedia()->getAbsolutePath('magestore/purchaseOrder/detail/product_to_import.csv');
            $data = array(
                array('Product', 'SKU', 'Supplier SKU', 'Qty', 'Qty Received',
                    'Qty Transferred', 'Qty Returned', 'Qty Billed',
                    'Purchase Cost', 'Tax(%)', 'Discount(%)', 'Amount')
            );

            $items = $purchaseOrder->getItems();

            $tmp = [];
            foreach ($items as $item) {
                $tmp[] = $this->generateItemData($item);
            }
            $data = array_merge($data, $tmp);

            $this->csvProcessor->saveData($filename, $data);
            return $this->fileFactory->create(
                $filenameDownload,
                file_get_contents($filename),
                DirectoryList::VAR_DIR
            );
        }
    }

    /**
     * get base dir media
     *
     * @return string
     */
    public function getBaseDirMedia()
    {
        return $this->filesystem->getDirectoryWrite('media');
    }

    /**
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface $item
     * @return array
     */
    public function generateItemData($item) {
        return [
            $item->getProductName(),
            $item->getProductSku(),
            $item->getProductSupplierSku(),
            $item->getQtyOrderred(),
            $item->getQtyReceived(),
            $item->getQtyTransferred(),
            $item->getQtyReturned(),
            $item->getQtyBilled(),
            $this->getPriceFormat($item->getCost()),
            $item->getTax(),
            $item->getDiscount(),
            $this->getItemTotal($item)
        ];
    }

    public function getPriceFormat($price){
        $currency = $this->currencyFactory->create()->load(
            $this->purchaseOrder->getCurrencyCode()
        );
        return $currency->formatTxt($price);
    }

    /**
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface $item
     */
    public function getItemTotal($item){
        $itemQty = $item->getQtyOrderred();
        $itemTotal = $itemQty * $item->getCost();
        $itemDiscount = $itemTotal*$item->getDiscount()/100;
        $taxType = $this->getTaxType();
        if($taxType == 0){
            $itemTax = $itemTotal*$item->getTax()/100;
        }else{
            $itemTax = ($itemTotal-$itemDiscount)*$item->getTax()/100;
        }
        return $this->getPriceFormat($itemTotal-$itemDiscount+$itemTax);
    }

    public function getTaxType(){
        $taxType = $this->taxShippingService->getTaxType();
        return $taxType;
    }
}