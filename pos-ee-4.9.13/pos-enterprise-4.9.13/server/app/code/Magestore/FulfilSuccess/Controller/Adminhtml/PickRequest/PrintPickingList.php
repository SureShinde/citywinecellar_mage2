<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Controller\Adminhtml\PickRequest;

use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action\Context;
use Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequest\CollectionFactory;
use Magestore\FulfilSuccess\Service\PickRequest\PickRequestPrintService;

class PrintPickingList extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    
    /**
     * @var PickRequestPrintService 
     */
    protected $printService;
  
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;    

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context, 
        Filter $filter, 
        CollectionFactory $collectionFactory, 
        PickRequestPrintService $printService,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->printService = $printService;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $groupType = $this->_request->getParam('type');
        $printData = $this->printService->printPickingList($collection->getItems(), $groupType);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('Picking List'));

        /* check if install barcode module */
        $isInstallBarcode = class_exists('\Zend_Barcode') || class_exists('\Zend\Barcode\Barcode');
        if(!$isInstallBarcode){
            $resultPage->getLayout()->getBlock('fulfilsuccess.pickrequest.printpickinglist')
            ->setTemplate('Magestore_FulfilSuccess::pickRequest/print/require_barcode.phtml');
        }else{
            $resultPage->getLayout()->getBlock('fulfilsuccess.pickrequest.printpickinglist')
                ->setPrintData($printData);
        }
        return $resultPage;
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->addBreadcrumb(__('Fulfillment Success'), __('Fulfillment Success'))
            ->addBreadcrumb(__('Picking Items'), __('Picking Items'));
        return $resultPage;
    }    
}
