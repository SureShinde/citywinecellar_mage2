<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Pos;
use Magestore\Webpos\Api\Data\Pos\PosInterface;

/**
 * Class StaffManagement
 * @package Magestore\Webpos\Model\Staff
 */
class PosRepository implements \Magestore\Webpos\Api\Pos\PosRepositoryInterface
{

    /**
     * @var  \Magestore\Webpos\Model\ResourceModel\Pos\Pos
     */
    protected $posResourceModel;
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Pos\Pos\CollectionFactory
     */
    protected $posResourceCollectionFactory;


    /**
     * @var \Magestore\Webpos\Api\Data\Pos\PosInterfaceFactory
     */
    protected $posFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magestore\Webpos\Api\Staff\SessionRepositoryInterface
     */
    protected $sessionRepository;

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Staff\Session
     */
    protected $sessionResource;

    /**
     * @var \Magestore\Webpos\Api\Data\Pos\PosSearchResultsInterfaceFactory
     */
    protected $posSearchResults;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;


    /**
     * PosRepository constructor.
     * @param \Magestore\Webpos\Model\ResourceModel\Pos\Pos $posResourceModel
     * @param \Magestore\Webpos\Api\Data\Pos\PosInterfaceFactory $posFactory
     * @param \Magestore\Webpos\Model\ResourceModel\Pos\Pos\CollectionFactory $posResourceCollection
     * @param \Magestore\Webpos\Api\Staff\SessionRepositoryInterface $sessionRepository
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magestore\Webpos\Model\ResourceModel\Staff\Session $sessionResource
     * @param \Magestore\Webpos\Api\Data\Pos\PosSearchResultsInterfaceFactory $posSearchResultsInterfaceFactory
     */
    public function __construct(
        \Magestore\Webpos\Model\ResourceModel\Pos\Pos $posResourceModel,
        \Magestore\Webpos\Api\Data\Pos\PosInterfaceFactory $posFactory,
        \Magestore\Webpos\Model\ResourceModel\Pos\Pos\CollectionFactory $posResourceCollection,
        \Magestore\Webpos\Api\Staff\SessionRepositoryInterface $sessionRepository,
        \Magento\Framework\App\RequestInterface $request,
        \Magestore\Webpos\Model\ResourceModel\Staff\Session $sessionResource,
        \Magestore\Webpos\Api\Data\Pos\PosSearchResultsInterfaceFactory $posSearchResultsInterfaceFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    )
    {
        $this->posResourceModel = $posResourceModel;
        $this->posFactory = $posFactory;
        $this->posResourceCollectionFactory = $posResourceCollection;
        $this->request = $request;
        $this->sessionRepository = $sessionRepository;
        $this->sessionResource = $sessionResource;
        $this->posSearchResults = $posSearchResultsInterfaceFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * Save staff.
     *
     * @param \Magestore\Webpos\Api\Data\Pos\PosInterface $pos
     * @return \Magestore\Webpos\Api\Data\Pos\PosInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\Webpos\Api\Data\Pos\PosInterface $pos) {
        try {
            $this->posResourceModel->save($pos);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__('Unable to save pos'));
        }
        return $pos;
    }

    /**
     * @inheritdoc
     */
    public function getAllPos() {
        /** @var \Magestore\Webpos\Model\ResourceModel\Pos\Pos\Collection $collection */
        $collection = $this->posResourceCollectionFactory->create();
        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($posId) {
        $pos = $this->posFactory->create();
        $this->posResourceModel->load($pos, $posId);
        if (!$pos->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Pos with id "%1" does not exist.', $posId));
        } else {
            return $pos;
        }
    }


    /**
     * Retrieve pos collection.
     *
     * @param int $locationId
     * @return \Magestore\Webpos\Api\Data\Pos\PosInterface[]
     */
    public function getPosByLocationId($locationId) {
        $posCollection =  $this->posResourceCollectionFactory->create()
            ->joinToStaffTable()
            ->addFieldToFilter(\Magestore\Webpos\Api\Data\Pos\PosInterface::LOCATION_ID, $locationId)
            ;
        return $posCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magestore\Webpos\Api\Data\Pos\PosInterface $pos)
    {
        return $this->deleteById($pos->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($posId)
    {
        $pos = $this->getById($posId);
        $salesCollection = $this->orderCollectionFactory->create()->addFieldToFilter('pos_id', $posId);
        if (!$salesCollection->getSize()) {
            $this->posResourceModel->delete($pos);
        } else {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__('The operations failed. Some POS are still working. You can\'t delete them!'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function assignPos($pos) {
        // TODO : update shift data
        $posModel = $this->posFactory->create();
        $this->posResourceModel->load($posModel, $pos->getPosId());
        if (!$posModel->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Pos with id '.$pos->getPosId().' does not exist.'));
        } else {
            try {
                $sessionId = $this->request->getParam(\Magestore\WebposIntegration\Controller\Rest\RequestProcessor::SESSION_PARAM_KEY);
                try {
                    $sessionLogin = $this->sessionRepository->getBySessionId($sessionId);
                    if ($sessionLogin) {
                        $sessionLogin->setLocationId($posModel->getLocationId())
                            ->setPosId($posModel->getPosId())
                            ->setHasException(0);
                        $this->sessionResource->save($sessionLogin);
                    } else {
                        throw new \Magento\Framework\Exception\NoSuchEntityException(__('The session does not exist.'));
                    }
                }catch(\Magento\Framework\Exception\LocalizedException $e){
                    throw new \Magento\Framework\Exception\LocalizedException(__('There are some problem when save session!'));
                }
                $posModel->setData('staff_id', $sessionLogin->getStaffId());
                $this->posResourceModel->save($posModel);
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\CouldNotSaveException(__('Unable to save pos'));
            }
        }
        return $posModel->setMessage('Enter to pos successfully');
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->posResourceCollectionFactory->create();
        //Add filters from root filter group to the collection
        /** @var FilterGroup $group */
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders === null) {
            $sortOrders = [];
        }
        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $collection->addOrder(
                $field,
                ($sortOrder->getDirection() == \Magento\Framework\Api\SortOrder::SORT_ASC)
                    ? \Magento\Framework\Api\SortOrder::SORT_ASC : \Magento\Framework\Api\SortOrder::SORT_DESC
            );
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->load();
        $searchResults = $this->posSearchResults->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }


    /**
     * @param int $posId
     * @return PosInterface
     * @throws \Exception
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function freePosById($posId)
    {
        $posModel = $this->posFactory->create();
        $this->posResourceModel->load($posModel, $posId);
        if (!$posModel->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Pos with id ' . $posId . ' does not exist.'));
        }

        $posModel->setData(PosInterface::STAFF_ID, null);
        $this->posResourceModel->save($posModel);
        return $posModel;
    }

}