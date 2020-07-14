<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder\Transferred;

/**
 * Class Save
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder\Transferred
 */
class Save extends \Magestore\PurchaseOrderSuccess\Controller\Adminhtml\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_PurchaseOrderSuccess::transferred_purchase_order';

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\PurchaseOrderService
     */
    protected $purchaseOrderService;

    /**
     * @var \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\Transferred\TransferredService
     */
    protected $transferredService;
    
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
        \Magestore\PurchaseOrderSuccess\Service\PurchaseOrder\Item\Transferred\TransferredService $transferredService,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Stdlib\DateTime\Filter\DateTime $dateFilter,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ){
        parent::__construct($context);
        $this->purchaseOrderService = $purchaseOrderService;
        $this->transferredService = $transferredService;
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
        if ($this->localeDate->date()->format('Y-m-d') != $params['transferred_at']) {
            $filterValues = ['transferred_at' => $this->dateFilter];
            $inputFilter = new \Zend_Filter_Input(
                $filterValues,
                [],
                $params
            );
            $params = $inputFilter->getUnescaped();
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        if(!$params['purchase_order_id']){
            $this->messageManager->addErrorMessage(__('Please select a purchase order to transfer received product'));
            return $resultRedirect->setPath('*/purchaseOrder/');
        }
        if(!isset($params['dynamic_grid'])){
            $this->messageManager->addErrorMessage(__('Please transfer at least one product.'));
            return $resultRedirect->setPath('*/purchaseOrder/view',['id'=>$params['purchase_order_id']]);
        }
        $transferredData = $this->transferredService->processTransferredData($params['dynamic_grid']);
        if(empty($transferredData)){
            $this->messageManager->addErrorMessage(__('Please transfer at least one product qty.'));
        }else {
            try {
                $user = $this->_auth->getUser();
                $transferStockItemData = $this->purchaseOrderService->transferProducts(
                   $transferredData, $params, $user->getUserName()
                );
                if(!empty($transferStockItemData)){
                    // Update stock to MSI instead of InventorySuccess
                    $this->transferredService->updateStock($transferStockItemData, $params);

                    $this->messageManager->addSuccessMessage(__('Transfer product(s) successfully.'));
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/purchaseOrder/view', ['id' => $params['purchase_order_id']]);
    }
    
}