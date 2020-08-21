<?php

namespace Magestore\PurchaseOrderSuccess\Controller\PurchaseOrder;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NotFoundException;

/**
 * Controller Downloadcsv
 *
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class Downloadcsv extends \Magestore\PurchaseOrderSuccess\Controller\AbstractController
{
    protected $purchaseOrder;

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $purchaseKey = $this->getRequest()->getParam('key');
        $purchaseOrder = $this->purchaseOrderRepository->getByKey($purchaseKey);
        $this->purchaseOrder = $purchaseOrder;
        if ($purchaseOrder && $purchaseOrder->getPurchaseOrderId()) {
            $filenameDownload = 'items-of-purchase-order-' . $purchaseOrder->getPurchaseCode() . '.csv';
            $this->getBaseDirMedia()->create('magestore/purchaseOrder/detail');
            $filename = $this->getBaseDirMedia()->getAbsolutePath(
                'magestore/purchaseOrder/detail/product_to_import.csv'
            );
            $data = [
                [
                    'Product',
                    'SKU',
                    'Supplier SKU',
                    'Qty',
                    'Qty Received',
                    'Qty Transferred',
                    'Qty Returned',
                    'Qty Billed',
                    'Purchase Cost',
                    'Tax(%)',
                    'Discount(%)',
                    'Amount'
                ]
            ];

            $items = $purchaseOrder->getItems();

            $tmp = [];
            foreach ($items as $item) {
                $tmp[] = $this->generateItemData($item);
            }
            $data = array_merge($data, $tmp);

            $this->csvProcessor->saveData($filename, $data);
            /** @var \Magento\Framework\Filesystem\DriverInterface $driverFile */
            $driverFile = $this->_objectManager->get(\Magento\Framework\Filesystem\DriverInterface::class);
            return $this->fileFactory->create(
                $filenameDownload,
                $driverFile->fileGetContents($filename),
                DirectoryList::VAR_DIR
            );
        } else {
            throw new NotFoundException(__('Could not download sample file.'));
        }
    }

    /**
     * Get base dir media
     *
     * @return string
     */
    public function getBaseDirMedia()
    {
        return $this->filesystem->getDirectoryWrite('media');
    }

    /**
     * Generate Item Data
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface $item
     * @return array
     */
    public function generateItemData($item)
    {
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

    /**
     * Get Price Format
     *
     * @param float $price
     * @return mixed
     */
    public function getPriceFormat($price)
    {
        $currency = $this->currencyFactory->create()->load(
            $this->purchaseOrder->getCurrencyCode()
        );
        return $currency->formatTxt($price);
    }

    /**
     * Get Item Total
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface $item
     */
    public function getItemTotal($item)
    {
        $itemQty = $item->getQtyOrderred();
        $itemTotal = $itemQty * $item->getCost();
        $itemDiscount = $itemTotal * $item->getDiscount() / 100;
        $taxType = $this->getTaxType();
        if ($taxType == 0) {
            $itemTax = $itemTotal * $item->getTax() / 100;
        } else {
            $itemTax = ($itemTotal - $itemDiscount) * $item->getTax() / 100;
        }
        return $this->getPriceFormat($itemTotal - $itemDiscount + $itemTax);
    }

    /**
     * Get Tax Type
     *
     * @return int
     */
    public function getTaxType()
    {
        $taxType = $this->taxShippingService->getTaxType();
        return $taxType;
    }
}
