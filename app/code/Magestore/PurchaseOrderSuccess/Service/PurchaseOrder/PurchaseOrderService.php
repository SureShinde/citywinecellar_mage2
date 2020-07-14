<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Service\PurchaseOrder;

use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface;
use Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderItemInterface;
use Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder;

class PurchaseOrderService
{
    const DEFAULT_ID = 1;
    const CODE_LENGTH = 8;
    const EMAIL_SEND_TO_SUPPLIER_TEMPLATE = 'purchaseordersuccess/email_template/email_to_supplier';

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * @var PurchaseOrderRepositoryInterface
     */
    protected $purchaseOrderRepository;

    /**
     * @var Item\ItemService
     */
    protected $purchaseItemService;

    /**
     * @var Item\Received\ReceivedService
     */
    protected $receivedService;

    /**
     * @var Item\Returned\ReturnedService
     */
    protected $returnedService;

    /**
     * @var Invoice\InvoiceService
     */
    protected $invoiceService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\Config\TaxAndShipping
     */
    protected $taxShippingService;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\PurchaseOrderCodeFactory
     */
    protected $purchaseOrderCodeFactory;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrderCode\PurchaseOrderCodeService
     */
    protected $purchaseOrderCodeService;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * PurchaseOrderService constructor.
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param PurchaseOrderRepositoryInterface $purchaseOrderRepository
     * @param Item\ItemService $purchaseItemService
     * @param Item\Received\ReceivedService $receivedService
     * @param Item\Returned\ReturnedService $returnedService
     * @param Item\Transferred\TransferredService $transferredService
     * @param Invoice\InvoiceService $invoiceService
     * @param \Magestore\PurchaseOrderSuccess\Service\Config\TaxAndShipping $taxShippingService
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magestore\PurchaseOrderSuccess\Model\PurchaseOrderCodeFactory $purchaseOrderCodeFactory
     * @param \Magestore\PurchaseOrderSuccess\Service\PurchaseOrderCode\PurchaseOrderCodeService $purchaseOrderCodeService
     */
    public function __construct(
        \Magento\Directory\Helper\Data $directoryHelper,
        PurchaseOrderRepositoryInterface $purchaseOrderRepository,
        Item\ItemService $purchaseItemService,
        Item\Received\ReceivedService $receivedService,
        Item\Returned\ReturnedService $returnedService,
        Item\Transferred\TransferredService $transferredService,
        Invoice\InvoiceService $invoiceService,
        \Magestore\PurchaseOrderSuccess\Service\Config\TaxAndShipping $taxShippingService,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrderCodeFactory $purchaseOrderCodeFactory,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrderCode\PurchaseOrderCodeService $purchaseOrderCodeService,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry
    ){
        $this->directoryHelper = $directoryHelper;
        $this->purchaseOrderRepository = $purchaseOrderRepository;
        $this->purchaseItemService = $purchaseItemService;
        $this->receivedService = $receivedService;
        $this->returnedService = $returnedService;
        $this->transferredService = $transferredService;
        $this->invoiceService = $invoiceService;
        $this->taxShippingService = $taxShippingService;
        $this->scopeConfig = $scopeConfig;
        $this->purchaseOrderCodeFactory = $purchaseOrderCodeFactory;
        $this->purchaseOrderCodeService = $purchaseOrderCodeService;
        $this->objectManager = $objectManager;
        $this->registry = $registry;
    }

    /**
     * @param PurchaseOrderInterface $purchaseOrderInterface
     * @return $this
     */
    public function getCurrencyCode(PurchaseOrderInterface $purchaseOrder){
        $baseCurrencyCode = $this->directoryHelper->getBaseCurrencyCode();
        $purchaseOrder->setCurrencyCode($baseCurrencyCode);
        return $purchaseOrder;
    }


    /**
     * Get purchase code
     *
     * @param PurchaseOrderInterface $purchaseOrder
     * @return PurchaseOrderInterface
     */
    public function getPurchaseCode(PurchaseOrderInterface $purchaseOrder){
        $type = $purchaseOrder->getType();
        $code = $purchaseOrder->getPurchaseCode();
        if($type==PurchaseOrder\Option\Type::TYPE_QUOTATION){
            if(!$code){
                $code = $this->purchaseOrderCodeService->generateCode(PurchaseOrder\Option\Code::QUOTATION_CODE_PREFIX);
            }
        }
        if($type==PurchaseOrder\Option\Type::TYPE_PURCHASE_ORDER){
            if (strpos($code, PurchaseOrder\Option\Code::PURCHASE_ORDER_CODE_PREFIX) === false) {
                $code = $this->purchaseOrderCodeService->generateCode(PurchaseOrder\Option\Code::PURCHASE_ORDER_CODE_PREFIX);
            }
        }
        $purchaseOrder->setPurchaseCode($code);
        return $purchaseOrder;
    }

    /**
     * @param PurchaseOrderInterface $purchaseOrder
     * @throws \Exception
     */
    public function updatePurchaseTotal(PurchaseOrderInterface $purchaseOrder){
        $taxType = $this->taxShippingService->getTaxType();
//        $defaulShippingCost = $this->taxShippingService->getDefaultShippingCost() * $purchaseOrder->getCurrencyRate();
        $purchaseItems = $this->purchaseItemService
            ->getProductsByPurchaseOrderId($purchaseOrder->getId())->getData();
        $totalQty = $subtotal = $discount = $tax = $grandTotalExclTax = $grandTotalInclTax = 0;
        $shippingCost = $purchaseOrder->getShippingCost();
//        if($shippingCost == 0 && $defaulShippingCost !== '' && $defaulShippingCost>0){
//            $purchaseOrder->setShippingCost($defaulShippingCost);
//            $shippingCost = $defaulShippingCost;
//        }
        foreach ($purchaseItems as $item){
            $itemQty = $item[PurchaseOrderItemInterface::QTY_ORDERRED];
            if(!$itemQty)
                continue;
            $totalQty += $itemQty;
            $itemTotal = ($itemQty * $item[PurchaseOrderItemInterface::COST]);
            $subtotal += $itemTotal;
            $itemDiscount = $itemTotal*$item[PurchaseOrderItemInterface::DISCOUNT]/100;
            $discount += $itemDiscount;
            if($taxType == 0){
                $taxItem = $itemTotal*$item[PurchaseOrderItemInterface::TAX]/100;
            }else{
                $taxItem = ($itemTotal-$itemDiscount)*$item[PurchaseOrderItemInterface::TAX]/100;
            }
            $tax += $taxItem;
        }
        if($totalQty==0){
            $purchaseOrder->setShippingCost(0);
        }
        $grandTotalExclTax = $subtotal-$discount+$shippingCost;
        $grandTotalInclTax = $grandTotalExclTax+$tax;
        $purchaseOrder->setTotalQtyOrderred($totalQty);
        $purchaseOrder->setSubtotal($subtotal);
        $purchaseOrder->setTotalDiscount(-$discount);
        $purchaseOrder->setTotalTax($tax);
        $purchaseOrder->setGrandTotalExclTax($grandTotalExclTax);
        $purchaseOrder->setGrandTotalInclTax($grandTotalInclTax);
        try{
            $this->purchaseOrderRepository->save($purchaseOrder);
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\PurchaseOrderInterface $purchaseOrder
     * @param \Magestore\SupplierSuccess\Api\Data\SupplierInterface $supplier
     * @return bool
     */
    public function sendEmailToSupplier($purchaseOrder, $supplier){
        /** @var \Magestore\PurchaseOrderSuccess\Model\Email\TransportBuilder $transportBuilder */
        $transportBuilder = \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magestore\PurchaseOrderSuccess\Model\Email\TransportBuilder'
        );
        try {
            if(class_exists('mPDF')) {
                $fileName = 'pub/media/PurchaseOrder.pdf';
                $html = $this->objectManager
                    ->create('Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email\EmailToSupplier\Header')->toHtml();
                $html .= $this->objectManager
                    ->create('Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email\EmailToSupplier\ItemsEmail')->toHtml();
                $html .= $this->objectManager
                    ->create('Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email\EmailToSupplier\Total')
                    ->setWidth('44%')->toHtml();

                $mPDF = $this->objectManager->create('mPDF');
                $mPDF->WriteHTML($html);
                $mPDF->Output($fileName, 'F');
            }
            $sender = [
                'name' => $this->scopeConfig->getValue('trans_email/ident_general/name'),
                'email' => $this->scopeConfig->getValue('trans_email/ident_general/email'),
            ];
            if(class_exists('mPDF')) {
                /* attach PDF */
                $transport = $transportBuilder
                    ->setTemplateIdentifier($this->scopeConfig->getValue(self::EMAIL_SEND_TO_SUPPLIER_TEMPLATE))
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        ]
                    )
                    ->setTemplateVars([
                        'purchase_order' => $purchaseOrder,
                        'supplier' => $supplier
                    ])
                    ->setFrom($sender)
                    ->addTo(trim($supplier->getContactEmail()))
                    ->attachFile($fileName, 'PurchaseOrder.pdf')
                    ->getTransport();
                $transport->sendMessage();
            }else{
                /* not attach PDF */
                $transport = $transportBuilder
                    ->setTemplateIdentifier($this->scopeConfig->getValue(self::EMAIL_SEND_TO_SUPPLIER_TEMPLATE))
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        ]
                    )
                    ->setTemplateVars([
                        'purchase_order' => $purchaseOrder,
                        'supplier' => $supplier
                    ])
                    ->setFrom($sender)
                    ->addTo(trim($supplier->getContactEmail()))
                    ->getTransport();
                $transport->sendMessage();
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Received all purchase order item
     *
     * @param int $purchaseId
     * @param string|null $receivedTime
     * @param string|null $createdBy
     * @return $this
     * @throws \Exception
     */
    public function receiveAllProduct($purchaseId, $receivedTime = null, $createdBy = null){
        $productSkus = [];
        $purchaseOrder = $this->purchaseOrderRepository->get($purchaseId);
        if(!$purchaseOrder || !$purchaseOrder->getPurchaseOrderId())
            throw new \Exception(__('Can not find this purchase order'));
        $purchaseItems = $purchaseOrder->getItems();
        foreach ($purchaseItems as $id => $item) {
            $result = $this->receivedService->receiveItem($purchaseOrder, $item, null, $receivedTime, $createdBy);
            if(!$result) {
                $productSkus[] = $item->getProductSku();
            } else {
                $this->addProductToSupplier($purchaseId, $purchaseOrder->getSupplierId(), $item->getProductId());
            }
        }
        $this->purchaseOrderRepository->save($purchaseOrder);
        if(!empty($productSkus))
            throw new \Exception(__('Can not receive products: %1', implode(', ',$productSkus)));
        return $this;
    }

    /**
     * @param $purchaseId
     * @param array $receivedData
     * @param string $receivedTime
     * @param null $createdBy
     * @return $this
     * @throws \Exception
     */
    public function receiveProducts($purchaseId, $receivedData = [], $receivedTime = null, $createdBy = null){
        $purchaseOrder = $this->purchaseOrderRepository->get($purchaseId);
        if(!$purchaseOrder || !$purchaseOrder->getPurchaseOrderId())
            throw new \Exception(__('Can not find this purchase order'));
        $purchaseItems = $this->purchaseItemService->getProductsByPurchaseOrderId($purchaseId, array_keys($receivedData));

        foreach ($purchaseItems as $item) {
            $productId = $item->getProductId();
            if(!in_array($productId, array_keys($receivedData)))
                continue;
            $result = $this->receivedService->receiveItem(
                $purchaseOrder, $item, $receivedData[$productId], $receivedTime, $createdBy
            );
            if(!$result) {
                $productSkus[] = $item->getProductSku();
            } else {
                $this->addProductToSupplier($purchaseId, $purchaseOrder->getSupplierId(), $productId);
            }
        }
        $this->purchaseOrderRepository->save($purchaseOrder);
        if(!empty($productSkus))
            throw new \Exception(__('Can not receive products: %1', implode(', ',$productSkus)));
        return $this;
    }

    public function addProductToSupplier($purchaseId, $supplierId, $productId) {
        /** @var \Magestore\SupplierSuccess\Service\Supplier\ProductService $supplierProductService */
        $supplierProductService = $this->objectManager->get('Magestore\SupplierSuccess\Service\Supplier\ProductService');
        $listPrdSupplier = $supplierProductService->getProductsBySupplierId($supplierId, [$productId]);
        if(!$listPrdSupplier->getSize()) {
            $collection = $this->purchaseItemService->getProductsByPurchaseOrderId($purchaseId, [$productId]);
            $collection->setPageSize(1)->setCurPage(1);
            $purchaseItems = $collection->getFirstItem();
            $data = [
                'supplier_id' => $supplierId,
                'product_id' => $productId,
                'product_sku' => $purchaseItems->getData('product_sku'),
                'product_name' => $purchaseItems->getData('product_name'),
                'product_supplier_sku' => $purchaseItems->getData('product_supplier_sku'),
                'cost' => $purchaseItems->getData('cost'),
                'tax' => $purchaseItems->getData('tax')
            ];
            $supplierProductService->assignProductToSupplier($data);
        }
    }

    /**
     * @param $purchaseId
     * @param array $returnedData
     * @param null $returnedTime
     * @param null $createdBy
     * @return $this
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function returnProducts($purchaseId, $returnedData = [], $returnedTime = null, $createdBy = null){
        $purchaseOrder = $this->purchaseOrderRepository->get($purchaseId);
        if(!$purchaseOrder || !$purchaseOrder->getPurchaseOrderId())
            throw new \Exception(__('Can not find this purchase order'));
        $purchaseItems = $this->purchaseItemService->getProductsByPurchaseOrderId($purchaseId, array_keys($returnedData));
        foreach ($purchaseItems as $item) {
            $productId = $item->getProductId();
            if(!in_array($productId, array_keys($returnedData)))
                continue;
            $result = $this->returnedService->returnItem(
                $purchaseOrder, $item, $returnedData[$productId], $returnedTime, $createdBy
            );
            if(!$result)
                $productSkus[] = $item->getProductSku();
        }
        $this->purchaseOrderRepository->save($purchaseOrder);
        if(!empty($productSkus))
            throw new \Exception(__('Can not receive products: %1', implode(', ',$productSkus)));
        return $this;
    }

    /**
     * @param array $transferredData
     * @param array $params
     * @param null $createdBy
     * @return array
     * @throws \Exception
     */
    public function transferProducts(
        $transferredData = [], $params = [], $createdBy = null
    ){
        $purchaseOrder = $this->purchaseOrderRepository->get($params['purchase_order_id']);
        if(!$purchaseOrder || !$purchaseOrder->getPurchaseOrderId())
            throw new \Exception(__('Can not find this purchase order'));
        $transferStockItemData = [];
        $purchaseItems = $this->purchaseItemService->getProductsByPurchaseOrderId($params['purchase_order_id'], array_keys($transferredData));
        foreach ($purchaseItems as $item) {
            $productId = $item->getProductId();
            if(!in_array($productId, array_keys($transferredData)))
                continue;
            $transferData = $this->transferredService->transferItem(
                $purchaseOrder, $item, $transferredData[$productId], $params, $createdBy
            );
            if($transferData)
                $transferStockItemData[] = $transferData;
        }
        $this->purchaseOrderRepository->save($purchaseOrder);
        if(empty($transferStockItemData))
            throw new \Exception(__('Can not transfer product.'));
        return $transferStockItemData;
    }

    /**
     * Create an invoice
     *
     * @param $purchaseId
     * @param array $invoiceData
     * @param null $invoiceTime
     * @param null $createdBy
     * @return $this
     * @throws \Exception
     */
    public function createInvoice($purchaseId, $invoiceData = [], $invoiceTime = null, $createdBy = null){
        $purchaseOrder = $this->purchaseOrderRepository->get($purchaseId);
        if(!$purchaseOrder || !$purchaseOrder->getPurchaseOrderId())
            throw new \Exception(__('Can not find this purchase order'));
        $purchaseItems = $this->purchaseItemService->getProductsByPurchaseOrderId($purchaseId, array_keys($invoiceData));
        if(empty($purchaseItems))
            throw new \Exception(__('Please create invoice for at least one product.'));
        $this->invoiceService->createInvoice($purchaseOrder, $purchaseItems, $invoiceData, $invoiceTime, $createdBy);
        return $this;
    }

    /**
     * Generate import purchase product data
     *
     * @param int $purchaseId
     * @param int $supplierId
     * @return array
     */
    public function generateImportData($purchaseId, $supplierId){
        $data = array();
        $productCollection = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\SupplierSuccess\Service\Supplier\ProductService')
            ->getProductsBySupplierId($supplierId)
            ->setPageSize(3)
            ->setCurPage(1);
        /**
         * @var \Magestore\SupplierSuccess\Api\Data\SupplierProductInterface $product
         */
        foreach ($productCollection as $product) {
            $data[]= array($product->getProductSku(), $product->getCost(), $product->getTax(), 0, 1);
        }
        return $data;
    }
}
