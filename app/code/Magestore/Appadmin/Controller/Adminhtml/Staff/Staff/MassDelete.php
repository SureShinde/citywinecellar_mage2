<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Controller\Adminhtml\Staff\Staff;

use Magento\Backend\App\Action\Context;
use Magestore\Appadmin\Model\ResourceModel\Staff\Staff\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Magestore\Appadmin\Model\ResourceModel\Staff\Staff\Collection;

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
     * @var \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface
     */
    protected $staffRepository;
    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface $staffRepository
    ) {
        $this->staffRepository = $staffRepository;
        parent::__construct($context, $filter, $collectionFactory);
    }

    /**
     * @param Collection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function massAction(Collection $collection)
    {
        $staffDeleted = 0;
        foreach ($collection as $staff) {
            $this->staffRepository->delete($staff);
            $staffDeleted++;
        }

        if ($staffDeleted) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were deleted.', $staffDeleted));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }

}
