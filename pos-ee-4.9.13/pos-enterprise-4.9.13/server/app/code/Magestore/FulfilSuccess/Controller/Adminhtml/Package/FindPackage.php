<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Controller\Adminhtml\Package;

class FindPackage extends \Magestore\FulfilSuccess\Controller\Adminhtml\Package
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    protected $packageRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magestore\FulfilSuccess\Api\PackageRepositoryInterface $packageRepository
    )
    {
        parent::__construct($context, $coreRegistry);
        $this->resultPageFactory = $resultPageFactory;
        $this->packageRepository = $packageRepository;
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $trackingNumber = $this->getRequest()->getParam('tracking_number');
        try {
            $packageId = $this->packageRepository->getByTrackingNumber($trackingNumber)->getPackageId();
        } catch (\Exception $exception) {
            return $this->getResponse()->setBody($exception->getMessage());
        }
        $resultPage = $this->resultPageFactory->create();
        $result = $resultPage->getLayout()->createBlock(
            'Magestore\FulfilSuccess\Block\Adminhtml\Package\PackageDetail', 'package_detail'
        )->setData(
            'package_id', $packageId
        )->toHtml();
        $this->getResponse()->setBody($result);
    }
}
