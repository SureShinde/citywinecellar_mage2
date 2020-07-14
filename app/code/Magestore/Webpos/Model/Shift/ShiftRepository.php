<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created by PhpStorm.
 * User: steve
 * Date: 06/06/2016
 * Time: 13:42
 */

namespace Magestore\Webpos\Model\Shift;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magestore\Webpos\Api\Data\Shift\ShiftInterface;
use Magestore\Webpos\Api\Data\Shift\ShiftSearchResultsInterfaceFactory as SearchResultFactory;
use Magento\Framework\Api\SortOrder;


class ShiftRepository implements \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface
{
    /** @var $shiftResource  \Magestore\Webpos\Model\ResourceModel\Shift\Shift */
    protected $shiftResource;

    /** @var $shiftFactory  \Magestore\Webpos\Model\Shift\ShiftFactory */
    protected $_shiftFactory;

    /** @var \Magestore\Webpos\Model\ResourceModel\Shift\Shift\CollectionFactory */
    protected $_shiftCollectionFactory;

    /** @var  \Magestore\Webpos\Helper\Shift */
    protected $_shiftHelper;

    /** @var  \Magestore\Webpos\Model\Pos\PosRepository */
    protected $posRepository;

    /** @var  SearchResultFactory */
    protected $searchResultFactory;

    /**
     * ShiftRepository constructor.
     * @param \Magestore\Webpos\Model\ResourceModel\Shift\Shift $shiftResource
     * @param ShiftFactory $shiftFactory
     * @param \Magestore\Webpos\Helper\Shift $shiftHelper
     * @param \Magestore\Webpos\Model\Pos\PosRepository $posRepository
     * @param SearchResultFactory $searchResultFactory
     */
    public function __construct(
        \Magestore\Webpos\Model\ResourceModel\Shift\Shift $shiftResource,
        \Magestore\Webpos\Model\Shift\ShiftFactory $shiftFactory,
        \Magestore\Webpos\Helper\Shift $shiftHelper,
        \Magestore\Webpos\Model\Pos\PosRepository $posRepository,
        \Magestore\Webpos\Model\ResourceModel\Shift\Shift\CollectionFactory $shiftCollectionFactory,
        SearchResultFactory $searchResultFactory
    )
    {
        $this->shiftResource = $shiftResource;
        $this->_shiftFactory = $shiftFactory;
        $this->_shiftHelper = $shiftHelper;
        $this->posRepository = $posRepository;
        $this->searchResultFactory = $searchResultFactory;
        $this->_shiftCollectionFactory = $shiftCollectionFactory;
    }


    /**
     * get a list of Shift for a specific staff_id.
     * Because in the frontend we just need to show all shift for "this week"
     * so we will return this week shift only.
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Shift\ShiftSearchResultsInterface
     * @throws \Magento\Framework\Exception\InputException
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria)
    {
        /** @var \Magestore\Webpos\Api\Data\Shift\ShiftSearchResultsInterface $searchResult */
        $searchResult = $this->searchResultFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $this->addFilterGroupToCollection($filterGroup, $searchResult);
        }


        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders === null) {
            $sortOrders = [];
        }
        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $searchResult->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }

        $this->addShiftTimeRangeFilter($searchResult);
        if ($searchCriteria->getCurrentPage()) {
            $searchResult->setCurPage($searchCriteria->getCurrentPage());
        }
        if ($searchCriteria->getPageSize()) {
            $searchResult->setPageSize($searchCriteria->getPageSize());
        }
        $searchResult->setSearchCriteria($searchCriteria);

        return $searchResult;

    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magestore\Webpos\Api\Data\Shift\ShiftSearchResultsInterface $searchResult
     */
    public function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magestore\Webpos\Api\Data\Shift\ShiftSearchResultsInterface $searchResult
    )
    {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $conditions[] = [$condition => $filter->getValue()];
            $fields[] = $filter->getField();
        }
        if ($fields) {
            $searchResult->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * create datetime range from, to
     * @param $searchResult
     */
    public function addShiftTimeRangeFilter($searchResult)
    {
        $from = $this->_shiftHelper->getSessionSinceDay();
        $openStatus = ShiftInterface::OPEN_STATUS;
        $searchResult->getSelect()->where("`opened_at` >= '$from' OR `status` = '$openStatus'");
    }

    /**
     * save a shift with its data in $shift (ShiftInterface)
     * @param \Magestore\Webpos\Api\Data\Shift\ShiftInterface $shift
     * @return mixed
     * @throws StateException
     * @throws CouldNotSaveException
     */
    public function save(ShiftInterface $shift)
    {
        $shiftIncrementId = $shift->getShiftIncrementId();
        $staffId = $shift->getStaffId();
        $posId = $shift->getPosId();
        $shiftModel = $this->_shiftFactory->create();

        if (!$shiftIncrementId) {
            throw new StateException(__('Shift increment id is required'));
        }

        if (!$staffId) {
            throw new StateException(__('Staff id is required'));
        }

        if (!$posId) {
            throw new StateException(__('Pos id is required'));
        }

        $shiftModel->load($shiftIncrementId, "shift_increment_id");

        if ($shiftModel->getShiftIncrementId()) {
            $shift->setId($shiftModel->getId());
        } else {
            $shift->setId(null);
            if ($this->posIsOpened($posId)) {
                throw new StateException(__('Please close your session before opening a new one'));
            }
        }

        try {
            $this->shiftResource->save($shift);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return array(
            $this->_shiftHelper->getShiftDataByIncrementId($shift->getShiftIncrementId())
        );
    }

    /**
     * get shit information
     * @param $shiftId
     * @return \Magestore\Webpos\Api\Data\Shift\ShiftInterface
     */
    public function get($shiftId)
    {
        $shiftModel = $this->_shiftFactory->create()->load($shiftId);
        return $shiftModel;
    }

    /**
     * check opened session/shift by staff id
     * @param $posId
     * @return bool
     */
    public function posIsOpened($posId)
    {
        $collection = $this->_shiftCollectionFactory->create();
        $collection
            ->addFieldToFilter(ShiftInterface::POS_ID, $posId)
            ->addFieldToFilter(ShiftInterface::STATUS, ShiftInterface::OPEN_STATUS);

        return $collection->getSize() > 0;
    }

    /**
     * Get current shift by pos id
     *
     * @param int $posId
     * @return \Magestore\Webpos\Api\Data\Shift\ShiftInterface|null
     */
    public function getCurrentShiftByPosId($posId)
    {
        /** @var \Magestore\Webpos\Model\ResourceModel\Shift\Shift\Collection $collection */
        $collection = $this->_shiftCollectionFactory->create();
        return $collection
            ->addFieldToFilter(ShiftInterface::POS_ID, $posId)
            ->addFieldToFilter(ShiftInterface::STATUS, ShiftInterface::OPEN_STATUS)
            ->setOrder(ShiftInterface::SHIFT_ID, "DESC")
            ->getFirstItem();
    }
}
