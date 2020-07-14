<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Pos;


/**
 * Class Pos
 * @package Magestore\Webpos\Model\Pos
 */
class Pos extends \Magento\Framework\Model\AbstractModel implements \Magestore\Webpos\Api\Data\Pos\PosInterface
{
    /**
     * @var \Magestore\Appadmin\Api\Event\DispatchServiceInterface
     */
    protected $dispatchService;
    /**
     * @var \Magestore\Webpos\Api\Pos\PosRepositoryInterface
     */
    protected $posRepository;

    protected $currentData = [];

    /**
     * Pos constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\Webpos\Model\ResourceModel\Pos\Pos $resource
     * @param \Magestore\Webpos\Model\ResourceModel\Pos\Pos\Collection $resourceCollection
     * @param \Magestore\Appadmin\Api\Event\DispatchServiceInterface $dispatchService
     * @param \Magestore\Webpos\Api\Pos\PosRepositoryInterface $posRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\Webpos\Model\ResourceModel\Pos\Pos $resource,
        \Magestore\Webpos\Model\ResourceModel\Pos\Pos\Collection $resourceCollection,
        \Magestore\Appadmin\Api\Event\DispatchServiceInterface $dispatchService,
        \Magestore\Webpos\Api\Pos\PosRepositoryInterface $posRepository,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->dispatchService = $dispatchService;
        $this->posRepository = $posRepository;
    }

    /**
     *  Get Pos Id
     * @return string|null
     */
    public function getPosId()
    {
        return $this->getData(self::POS_ID);
    }

    /**
     * Set Pos Id
     *
     * @param string $posId
     * @return $this
     */
    public function setPosId($posId)
    {
        return $this->setData(self::POS_ID, $posId);
    }

    /**
     *  Get Pos Name
     * @return string|null
     */
    public function getPosName()
    {
        return $this->getData(self::POS_NAME);
    }

    /**
     * Set Pos Name
     *
     * @param string $posName
     * @return $this
     */
    public function setPosName($posName)
    {
        return $this->setData(self::POS_NAME, $posName);
    }
    /**
     *  location_id
     * @return int|null
     */
    public function getLocationId()
    {
        return $this->getData(self::LOCATION_ID);
    }

    /**
     * Set Location Id
     *
     * @param int $locationId
     * @return $this
     */
    public function setLocationId($locationId)
    {
        return $this->setData(self::LOCATION_ID, $locationId);
    }


    /**
     *  Staff Id
     * @return int|null
     */
    public function getStaffId()
    {
        return $this->getData(self::STAFF_ID);
    }

    /**
     * Set Staff Id
     *
     * @param int $staff_id
     * @return $this
     */
    public function setStaffId($staff_id)
    {
        return $this->setData(self::STAFF_ID, $staff_id);
    }

    /**
     * status
     * @return int
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * set Status
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Denominations
     * @param string $denominationIds
     * @return $this
     */
    public function setDenominationIds($denominationIds)
    {
        return $this->setData(self::DENOMINATION_IDS, $denominationIds);
    }

    /**
     * Denominations
     * @return string
     */
    public function getDenominationIds(){
        return $this->getData(self::DENOMINATION_IDS);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        if(!$this->isObjectNew()) {
            $currentObject = $this->posRepository->getById($this->getPosId());
            $this->currentData = $currentObject->getData();
        }
        return parent::beforeSave();
    }

    /**
     * @return $this
     * @throws \Exception
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function afterSave()
    {
        if(!$this->isObjectNew()) {
            // when disable pos or change location
            if(($this->getStatus() == \Magestore\Webpos\Model\Source\Adminhtml\Status::STATUS_DISABLED && $this->hasDataChangeForField('status'))
                || $this->hasDataChangeForField('location_id')) {
                $this->dispatchService->dispatchEventForceChangePos($this->currentData['staff_id'], $this->getPosId());
                $this->setStaffId(null);
                $this->_resource->save($this);
            }
        }
        return parent::afterSave();
    }

    public function hasDataChangeForField($fieldName) {
        return !($this->currentData[$fieldName] == $this->getData($fieldName));
    }

    public function beforeDelete()
    {
        $this->dispatchService->dispatchEventForceChangePos($this->getStaffId(), $this->getPosId());
        return parent::beforeDelete();
    }
    /**
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Pos\PosExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}