<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Location\Location;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Location\Location\Index
 *
 * List staff
 * Methods:
 *  execute
 *
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Location\Location
 * @module      Webpos
 * @author      Magestore Developer
 */
class Index extends \Magestore\Webpos\Controller\Adminhtml\Location\Location
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $registry,
        \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository,
        \Magestore\Webpos\Api\Data\Location\LocationInterfaceFactory $locationInterfaceFactory,
        \Magento\Framework\Module\Manager $moduleManager
        )
    {
        parent::__construct($context, $resultPageFactory, $resultJsonFactory, $resultLayoutFactory, $resultForwardFactory, $registry, $locationRepository, $locationInterfaceFactory);
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if($this->moduleManager->isEnabled('Magestore_InventorySuccess')) {
            return $this->redirectResult('inventorysuccess/warehouse/index');
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Webpos::locations');
        $resultPage->addBreadcrumb(__('Location'), __('Location'));
        $resultPage->getConfig()->getTitle()->prepend(__('Location'));
        return $resultPage;
    }
}