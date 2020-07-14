<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Controller\Adminhtml\Location;

use Magento\Framework\Controller\ResultFactory;

abstract class Location extends \Magento\Backend\App\Action {

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magestore\Webpos\Api\Location\LocationRepositoryInterface
     */
    protected $locationRepository;

    /**
     * @var \Magestore\Webpos\Api\Data\Location\LocationInterfaceFactory
     */
    protected $locationInterfaceFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    protected $resource = 'Magestore_Webpos::locations';

    /**
     * Location constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository
     * @param \Magestore\Webpos\Api\Data\Location\LocationInterfaceFactory $locationInterfaceFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $registry,
        \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository,
        \Magestore\Webpos\Api\Data\Location\LocationInterfaceFactory $locationInterfaceFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->locationRepository = $locationRepository;
        $this->locationInterfaceFactory = $locationInterfaceFactory;
        $this->registry = $registry;
        parent::__construct($context);
    }

    public function redirectResult($url, $params = []) {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($url, $params);

        return $resultRedirect;
    }

    public function jsonResult($data) {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($data);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed($this->resource);
    }
}