<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magestore\BarcodeSuccess\Helper\Data;
use Magestore\BarcodeSuccess\Model\Locator\LocatorInterface;


/**
 * Class AbstractIndex
 * @package Magestore\BarcodeSuccess\Controller\Adminhtml
 */
abstract class AbstractIndex extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Magestore\BarcodeSuccess\Helper\Data
     */
    protected $helper;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * AbstractIndex constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $data,
        LocatorInterface $locator
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $data;
        $this->locator = $locator;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_BarcodeSuccess::barcode');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Barcodes'));
        return $resultPage;
    }

    /**
     * @param $barcode
     */
    protected  function saveBarcode($data){
        $barcode = $this->helper->getModel('\Magestore\BarcodeSuccess\Api\Data\BarcodeInterface');
        try{
            $barcode->setData($data);
            $this->helper->resource->save($barcode);
        }catch (\Exception $e){
            $this->helper->addLog($e->getMessage());
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }

    /**
     * @param $totalQty
     * @param string $reason
     * @return string
     */
    public function saveHistory($totalQty, $type, $reason = ''){
        $historyId = '';
        $history = $this->helper->getModel('Magestore\BarcodeSuccess\Api\Data\HistoryInterface');
        $historyResource = $this->helper->getModel('Magestore\BarcodeSuccess\Model\ResourceModel\History');
        $adminSession = $this->helper->getModel('Magento\Backend\Model\Auth\Session');
        try{
            $admin = $adminSession->getUser();
            $adminId = ($admin)?$admin->getId():0;
            $history->setData('type',$type);
            $history->setData('reason',$reason);
            $history->setData('created_by',$adminId);
            $history->setData('total_qty',$totalQty);
            $historyResource->save($history);
            $historyId =  $history->getId();
        }catch (\Exception $e){
            $this->helper->addLog($e->getMessage());
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $historyId;
    }

    /**
     * @param string $historyId
     */
    public function removeHistory($historyId){
        $history = $this->helper->getModel('Magestore\BarcodeSuccess\Api\Data\HistoryInterface');
        $historyResource = $this->helper->getModel('Magestore\BarcodeSuccess\Model\ResourceModel\History');
        try{
            $historyResource->load($history, $historyId);
            if($history->getId()){
                $historyResource->delete($history);
            }
        }catch (\Exception $e){
            $this->helper->addLog($e->getMessage());
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }

    /**
     *
     * @param string $class
     * @param string $name
     * @param string $template
     * @return block type
     */
    public function createBlock($class,$name = '',$template = ""){
        $block = "";
        try{
            $block = $this->_view->getLayout()->createBlock($class,$name);
            if($block && $template != ""){
                $block->setTemplate($template);
            }
        }catch(\Exception $e){
            return $block;
        }
        return $block;
    }
}
