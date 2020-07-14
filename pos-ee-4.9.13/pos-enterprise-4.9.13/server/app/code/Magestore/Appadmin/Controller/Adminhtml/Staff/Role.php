<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Controller\Adminhtml\Staff;
/**
 * class \Magestore\Appadmin\Controller\Adminhtml\Staff\Role
 *
 * Abstract role action class
 * Methods:
 *  _isAllowed
 *
 * @category    Magestore
 * @package     Magestore\Appadmin\Controller\Adminhtml\Staff
 * @module      Appadmin
 * @author      Magestore Developer
 */
abstract class Role extends \Magento\Backend\App\Action
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
     * @var \Magestore\Appadmin\Api\Staff\RoleRepositoryInterface
     */
    protected $roleRepository;
    /**
     * @var \Magestore\Appadmin\Api\Data\Staff\RoleInterfaceFactory
     */
    protected $roleInterfaceFactory;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface
     */
    protected $staffRepository;
    /**
     * @var \Magestore\Appadmin\Api\Data\Staff\StaffInterfaceFactory
     */
    protected $staffInterfaceFactory;

    /**
     * @var \Magestore\Appadmin\Model\Staff\RoleFactory
     */
    protected $_roleFactory;

    /**
     * @var \Magestore\Appadmin\Model\ResourceModel\Staff\Staff\CollectionFactory
     */
    protected $_staffCollectionFactory;
    /**
     * @var \Magestore\Appadmin\Api\Event\DispatchServiceInterface
     */
    protected $dispatchService;

    /**
     * Role constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\Appadmin\Api\Staff\RoleRepositoryInterface $roleRepository
     * @param \Magestore\Appadmin\Api\Data\Staff\RoleInterfaceFactory $roleInterfaceFactory
     * @param \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface $staffRepository
     * @param \Magestore\Appadmin\Api\Data\Staff\StaffInterfaceFactory $staffInterfaceFactory
     * @param \Magestore\Appadmin\Model\ResourceModel\Staff\Staff\CollectionFactory $staffCollectionFactory
     * @param \Magestore\Appadmin\Model\Staff\RoleFactory $roleFactory
     * @param \Magestore\Appadmin\Api\Event\DispatchServiceInterface $dispatchService
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $registry,
        \Magestore\Appadmin\Api\Staff\RoleRepositoryInterface $roleRepository,
        \Magestore\Appadmin\Api\Data\Staff\RoleInterfaceFactory $roleInterfaceFactory,
        \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface $staffRepository,
        \Magestore\Appadmin\Api\Data\Staff\StaffInterfaceFactory $staffInterfaceFactory,
        \Magestore\Appadmin\Model\ResourceModel\Staff\Staff\CollectionFactory $staffCollectionFactory,
        \Magestore\Appadmin\Model\Staff\RoleFactory $roleFactory,
        \Magestore\Appadmin\Api\Event\DispatchServiceInterface $dispatchService
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->roleRepository = $roleRepository;
        $this->_roleFactory = $roleFactory;
        $this->roleInterfaceFactory = $roleInterfaceFactory;
        $this->staffRepository = $staffRepository;
        $this->staffInterfaceFactory = $staffInterfaceFactory;
        $this->_staffCollectionFactory = $staffCollectionFactory;
        $this->registry = $registry;
        $this->dispatchService = $dispatchService;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Appadmin::manageRoles');
    }

}