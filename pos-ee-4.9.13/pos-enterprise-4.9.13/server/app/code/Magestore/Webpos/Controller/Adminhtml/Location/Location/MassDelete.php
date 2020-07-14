<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Controller\Adminhtml\Location\Location;

use Magento\Backend\App\Action\Context;
use Magestore\Webpos\Api\Pos\PosRepositoryInterface;
use Magestore\Webpos\Model\Pos\PosRepository;
use Magestore\Webpos\Model\ResourceModel\Location\Location\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Magestore\Webpos\Model\ResourceModel\Location\Location\Collection;
use Magestore\Webpos\Api\Location\LocationRepositoryInterface;

/**
 * class \Magestore\Webpos\Controller\Adminhtml\Location\Location\MassDelete
 *
 * Mass delete
 * Methods:
 *  massAction
 *
 * @category    Magestore
 * @package     Magestore\Webpos\Controller\Adminhtml\Staff\Staff
 * @module      Webpos
 * @author      Magestore Developer
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var string
     */
    protected $redirectUrl = '*/*/index';
    /**
     * @var Filter
     */
    protected $filter;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var LocationRepositoryInterface
     */
    protected $locationRepositoryInterface;

    /**
     * @var $posRepositoryInterface
     */
    protected $posRepositoryInterface;

    /**
     * MassDelete constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param LocationRepositoryInterface $locationRepositoryInterface
     * @param PosRepositoryInterface $posRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        LocationRepositoryInterface $locationRepositoryInterface,
        \Magestore\Webpos\Api\Pos\PosRepositoryInterface $posRepository
    )
    {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->locationRepositoryInterface = $locationRepositoryInterface;
        $this->posRepositoryInterface = $posRepository;
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|mixed
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            return $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath($this->redirectUrl);
        }
    }


    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Webpos::location_delete');
    }

    /**
     * @param Collection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(Collection $collection)
    {
        $checkDelete = true;
        foreach ($collection as $location) {
            if (count($this->posRepositoryInterface->getPosByLocationId($location->getLocationId())) > 0) {
                $checkDelete = false;
                break;
            }
        }
        if ($checkDelete) {
            $locationDeleted = 0;
            foreach ($collection as $location) {
                $this->locationRepositoryInterface->delete($location);
                $locationDeleted++;
            }
            if ($locationDeleted) {
                $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were deleted.', $locationDeleted));
            }
        } else {
            $this->messageManager->addErrorMessage(__('The operations failed. Some locations are still working. You can\'t delete them!'));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->redirectUrl);

        return $resultRedirect;
    }

}
