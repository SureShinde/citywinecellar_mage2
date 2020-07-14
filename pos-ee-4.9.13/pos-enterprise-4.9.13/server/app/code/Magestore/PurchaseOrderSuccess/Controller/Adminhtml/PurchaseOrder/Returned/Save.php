<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder\Returned;

/**
 * Class Save
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder\Returned
 */
class Save extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::returned_purchase_order';

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService
     */
    protected $purchaseOrderService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\Returned\ReturnedService
     */
    protected $returnedService;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\DateTime
     */
    protected $dateFilter;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    public function __construct(
        \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\Context $context,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService $purchaseOrderService,
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\Returned\ReturnedService $returnedService,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Stdlib\DateTime\Filter\DateTime $dateFilter,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ){
        parent::__construct($context);
        $this->purchaseOrderService = $purchaseOrderService;
        $this->returnedService = $returnedService;
        $this->timezone = $timezone;
        $this->dateFilter = $dateFilter;
        $this->localeDate = $localeDate;
    }
    
    /**
     * Quotation grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        if ($this->localeDate->date()->format('Y-m-d') != $params['returned_at']) {
            $filterValues = ['returned_at' => $this->dateFilter];
            $inputFilter = new \Zend_Filter_Input(
                $filterValues,
                [],
                $params
            );
            $params = $inputFilter->getUnescaped();
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        if(!$params['purchase_order_id']){
            $this->messageManager->addErrorMessage(__('Please select a purchase order to return product'));
            return $resultRedirect->setPath('*/purchaseOrder/');
        }
        if(!isset($params['dynamic_grid'])){
            $this->messageManager->addErrorMessage(__('Please return at least one product.'));
            return $resultRedirect->setPath('*/purchaseOrder/view',['id'=>$params['purchase_order_id']]);
        }
        $returnedData = $this->returnedService->processReturnedData($params['dynamic_grid']);
        try {
            $user = $this->_auth->getUser();
            $this->purchaseOrderService->returnProducts(
                $params['purchase_order_id'], $returnedData, $params['returned_at'], $user->getUserName()
            );
            $this->messageManager->addSuccessMessage(__('Return product(s) successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $resultRedirect->setPath('*/purchaseOrder/view', ['id' => $params['purchase_order_id']]);
    }
    
}