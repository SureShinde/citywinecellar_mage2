<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Controller\Adminhtml\Staff\Role;

use Magento\Backend\App\Action;

/**
 * class \Magestore\Appadmin\Controller\Adminhtml\Staff\Staff\InlineEdit
 *
 * Inline edit user
 * Methods:
 *  _isAllowed
 *  execute
 *  getErrorMessages
 *  isErrorExists
 *
 * @category    Magestore
 * @package     Magestore\Appadmin\Controller\Adminhtml\Staff\Staff
 * @module      Appadmin
 * @author      Magestore Developer
 */
class InlineEdit extends \Magento\Backend\App\Action
{

    /** @var \Magento\Framework\Controller\Result\JsonFactory */
    protected $resultJsonFactory;

    /** @var \Magento\Framework\Api\DataObjectHelper */
    protected $dataObjectHelper;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var \Magestore\Appadmin\Api\Data\Staff\RoleInterfaceFactory */
    protected $roleInterfaceFactory;

    /**
     * @var \Magestore\Appadmin\Api\Staff\RoleRepositoryInterface
     */
    protected $roleRepository;

    /**
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magestore\Appadmin\Api\Data\Staff\RoleInterfaceFactory $roleInterfaceFactory,
        \Magestore\Appadmin\Api\Staff\RoleRepositoryInterface $roleRepository,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->roleInterfaceFactory = $roleInterfaceFactory;
        $this->roleRepository = $roleRepository;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        $model = $this->roleInterfaceFactory->create();
        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $staffId) {
            $model->setData($postItems[$staffId]);
            $model->setId($staffId);
            $this->roleRepository->save($model);
        }

        return $resultJson->setData([
            'messages' => $this->getErrorMessages(),
            'error' => $this->isErrorExists(),

        ]);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Appadmin::manageRoles');
    }


    /**
     * @return array
     */
    public function getErrorMessages()
    {
        $messages = [];
        foreach ($this->getMessageManager()->getMessages()->getItems() as $error) {
            $messages[] = $error->getText();
        }
        return $messages;
    }

    /**
     * @return bool
     */
    public function isErrorExists()
    {
        return (bool)$this->getMessageManager()->getMessages(true)->getCount();
    }

}
