<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Service\ReturnOrder;

use Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface;
use Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemInterface;
use Magestore\PurchaseOrderSuccess\Api\ReturnOrderRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Model\ReturnOrder;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder;

class ReturnOrderService {

    const DEFAULT_ID = 1;
    const CODE_LENGTH = 8;
    const EMAIL_SEND_TO_SUPPLIER_TEMPLATE = 'purchaseordersuccess/email_template/return_email_to_supplier';

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * @var ReturnOrderRepositoryInterface
     */
    protected $returnOrderRepository;

    /**
     * @var Item\ItemService
     */
    protected $returnItemService;

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
     * ReturnOrderService constructor.
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param ReturnOrderRepositoryInterface $returnOrderRepository
     * @param Item\ItemService $returnItemService
     * @param Item\Transferred\TransferredService $transferredService
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magestore\PurchaseOrderSuccess\Model\PurchaseOrderCodeFactory $purchaseOrderCodeFactory
     * @param \Magestore\PurchaseOrderSuccess\Service\PurchaseOrderCode\PurchaseOrderCodeService $purchaseOrderCodeService
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Directory\Helper\Data $directoryHelper,
        ReturnOrderRepositoryInterface $returnOrderRepository,
        Item\ItemService $returnItemService,
        Item\Transferred\TransferredService $transferredService,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrderCodeFactory $purchaseOrderCodeFactory,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrderCode\PurchaseOrderCodeService $purchaseOrderCodeService,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry
    ){
        $this->directoryHelper = $directoryHelper;
        $this->returnOrderRepository = $returnOrderRepository;
        $this->returnItemService = $returnItemService;
        $this->transferredService = $transferredService;
        $this->scopeConfig = $scopeConfig;
        $this->purchaseOrderCodeFactory = $purchaseOrderCodeFactory;
        $this->purchaseOrderCodeService = $purchaseOrderCodeService;
        $this->objectManager = $objectManager;
        $this->registry = $registry;
    }

    /**
     * Get purchase code
     *
     * @param ReturnOrderInterface $returnOrder
     * @return ReturnOrderInterface
     */
    public function getReturnCode(ReturnOrderInterface $returnOrder){
        $code = $returnOrder->getReturnCode();
        if(!$code) {
            $code = $this->purchaseOrderCodeService->generateCode(PurchaseOrder\Option\Code::RETURN_ORDER_CODE_PREFIX);
        }
        $returnOrder->setReturnCode($code);
        return $returnOrder;
    }

    /**
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderInterface $returnOrder
     * @param \Magestore\SupplierSuccess\Api\Data\SupplierInterface $supplier
     * @return bool
     */
    public function sendEmailToSupplier($returnOrder, $supplier){
        /** @var \Magestore\PurchaseOrderSuccess\Model\Email\TransportBuilder $transportBuilder */
        $transportBuilder = \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magestore\PurchaseOrderSuccess\Model\Email\TransportBuilder'
        );
        try {
            if(class_exists('mPDF')) {
                $fileName = 'pub/media/ReturnOrder.pdf';
                $html = $this->objectManager
                    ->create('Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email\EmailToSupplier\ReturnHeaderEmail')->toHtml();
                $html .= $this->objectManager
                    ->create('Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email\EmailToSupplier\ReturnItemsEmail')
                    ->setWidth('44%')->toHtml();
//                $html .= $this->objectManager
//                    ->create('Magestore\PurchaseOrderSuccess\Block\Adminhtml\Email\EmailToSupplier\Total')


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
                        'return_order' => $returnOrder,
                        'supplier' => $supplier
                    ])
                    ->setFrom($sender)
                    ->addTo(trim($supplier->getContactEmail()))
                    ->attachFile($fileName, 'ReturnOrder.pdf')
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
                        'return_order' => $returnOrder,
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
     * @param array $transferredData
     * @param array $params
     * @param null $createdBy
     * @return array
     * @throws \Exception
     */
    public function transferProducts(
        $transferredData = [], $params = [], $createdBy = null
    ){
        $returnOrder = $this->returnOrderRepository->get($params['return_id']);
        if(!$returnOrder || !$returnOrder->getReturnOrderId())
            throw new \Exception(__('Can not find this return order'));
        $transferStockItemData = [];
        $returnItems = $this->returnItemService->getProductsByReturnOrderId($params['return_id'], array_keys($transferredData));
        foreach ($returnItems as $item) {
            $productId = $item->getProductId();
            if(!in_array($productId, array_keys($transferredData)))
                continue;
            $transferData = $this->transferredService->transferItem(
                $returnOrder, $item, $transferredData[$productId], $params, $createdBy
            );
            if($transferData)
                $transferStockItemData[] = $transferData;
        }
        $this->returnOrderRepository->save($returnOrder);
        if(empty($transferStockItemData))
            throw new \Exception(__('Can not transfer product.'));
        return $transferStockItemData;
    }

    /**
     * Generate import return product data
     *
     * @param int $returnId
     * @param int $supplierId
     * @return array
     */
    public function generateImportData($returnId, $supplierId){
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
            $data[]= array($product->getProductSku(), 1);
        }
        return $data;
    }

    public function updateQtyReturnOrder(ReturnOrderInterface $returnOrder) {
        $returnItems = $this->returnItemService->getProductsByReturnOrderId($returnOrder->getReturnOrderId());
        $totalQty = 0;
        foreach ($returnItems as $item) {
            $totalQty += $item->getQtyReturned()*1;
        }
        $returnOrder->setTotalQtyReturned($totalQty);
        $this->returnOrderRepository->save($returnOrder);
    }
}