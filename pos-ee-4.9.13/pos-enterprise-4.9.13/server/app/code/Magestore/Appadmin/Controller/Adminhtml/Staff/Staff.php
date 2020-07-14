<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Appadmin\Controller\Adminhtml\Staff;
/**
 * class \Magestore\Webpos\Controller\Adminhtml\Staff\Staff
 *
 * Abstract Staff action class
 * Methods:
 *  _isAllowed
 *
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Staff
 * @module      Webpos
 * @author      Magestore Developer
 */
abstract class Staff extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface
     */
    protected $staffRepository;
    /**
     * @var \Magestore\Appadmin\Api\Data\Staff\StaffInterfaceFactory
     */
    protected $staffInterfaceFactory;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $registry,
        \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface $staffRepository,
        \Magestore\Appadmin\Api\Data\Staff\StaffInterfaceFactory $staffInterfaceFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->staffRepository = $staffRepository;
        $this->staffInterfaceFactory = $staffInterfaceFactory;
        $this->registry = $registry;
        parent::__construct($context);
    }
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Appadmin::manageStaffs');
    }
}