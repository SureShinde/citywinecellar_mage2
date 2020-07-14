<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Controller\Adminhtml\HistoricalStock;
    /**
     * Class Generate
     * @package Magestore\ReportSuccess\Controller\Adminhtml\HistoricalStock
     */
/**
 * Class Generate
 * @package Magestore\ReportSuccess\Controller\Adminhtml\HistoricalStock
 */
class Generate extends \Magestore\ReportSuccess\Controller\Adminhtml\Inventory
{

    /**
     * @var \Magestore\ReportSuccess\Model\CronManualFactory
     */
    protected $cronManualFactory;

    /**
     * Generate constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magestore\ReportSuccess\Model\CronManualFactory $cronManualFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magestore\ReportSuccess\Model\CronManualFactory $cronManualFactory
    )
    {
        parent::__construct(
            $context, $resultPageFactory, $resultJsonFactory, $resultLayoutFactory, $resultForwardFactory
        );
        $this->cronManualFactory = $cronManualFactory;
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $locationCode = $this->getRequest()->getParam('location_code');
        $cronManualModel = $this->cronManualFactory->create()->setData('location_code', $locationCode);
        $this->messageManager->addSuccessMessage(__('Your request has been submitted'));
        $cronManualModel->save();
        return $resultRedirect->setPath('omcreports/inventory/historicalStock');
    }
}