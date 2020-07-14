<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;

/**
 * Class View
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Quotation
 */
class View extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::view_purchase_order';
    
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface
     */
    protected $purchaseOrderRepository;
    
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context,
        \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepository,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ){
        parent::__construct($context);   
        $this->purchaseOrderRepository = $purchaseOrderRepository;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * View purchase order form
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $type = $this->getRequest()->getParam('type', Type::TYPE_PURCHASE_ORDER);
        $typeLabel = $this->getTypeLabel($type);
        $id = $this->getRequest()->getParam('id', null);
        if($id){
            try{
                $purchaseOrder = $this->purchaseOrderRepository->get($id, $typeLabel);
                if($purchaseOrder->getType()!=$type){
                    $message = $this->purchaseOrderRepository->getNotFoundExeptionMessage($typeLabel);
                    return $this->redirectGrid($type, $message);
                }
            }catch (\Exception $e){
                return $this->redirectGrid($type, $e->getMessage());
            }
        }else{
            $purchaseOrder = $this->_purchaseOrderFactory->create();
        }
        $this->_registry->register('current_purchase_order', $purchaseOrder);
        $resultPage = $this->_initAction();
        if($id){
            $code = $purchaseOrder->getPurchaseCode();
            $code = $code?$code:$id;
            $resultPage->getConfig()->getTitle()->prepend(__('View %1 #%2', $typeLabel, $code));
        }else{
            $resultPage->getConfig()->getTitle()->prepend(__('New %1 ', $typeLabel));
        }
        return $resultPage;
    }
}