<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Quotation;

/**
 * Class Convert
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder
 */
class Convert extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::convert_quotation';
    
    /**
     * @var \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface
     */
    protected $purchaseOrderRepository;

    
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context,
        \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepository
    ){
        parent::__construct($context);
        $this->purchaseOrderRepository = $purchaseOrderRepository;
    }
    
    /**
     * Quotation grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $purchaseId = $this->getRequest()->getParam('purchase_order_id');
        $type = $this->getRequest()->getParam('type');
        if($this->purchaseOrderRepository->convert($purchaseId)){
            return $this->redirectForm(
                \Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type::TYPE_PURCHASE_ORDER, 
                $purchaseId,
                __('Convert to purchase order successfully.')
            );
        }else{
            return $this->redirectForm(
                $type, 
                $purchaseId, 
                __('Can not convert to purchase order'),
                \Magento\Framework\Message\MessageInterface::TYPE_ERROR
            );
        }
    }
    
}