<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Controller\Adminhtml\Staff\Role;

use Magento\Backend\App\Action\Context;
use Magestore\Appadmin\Model\ResourceModel\Staff\Role\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Magestore\Appadmin\Model\ResourceModel\Staff\Role\Collection;

/**
 * class \Magestore\Appadmin\Controller\Adminhtml\Staff\Staff\MassDelete
 *
 * Mass delete
 * Methods:
 *  massAction
 *
 * @category    Magestore
 * @package     Magestore\Appadmin\Controller\Adminhtml\Staff\Staff
 * @module      Appadmin
 * @author      Magestore Developer
 */
class MassDelete extends AbstractMassAction
{
    /**
     * @var \Magestore\Appadmin\Api\Staff\RoleRepositoryInterface
     */
    protected $roleRepository;
    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Magestore\Appadmin\Api\Staff\RoleRepositoryInterface $roleRepository
    ) {
        $this->roleRepository = $roleRepository;
        parent::__construct($context, $filter, $collectionFactory);
    }

    /**
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(Collection $collection)
    {
        $roleDeleted = 0;
        foreach ($collection as $role) {
            $this->roleRepository->delete($role);
            $roleDeleted++;
        }

        if ($roleDeleted) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were deleted.', $roleDeleted));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }

}
