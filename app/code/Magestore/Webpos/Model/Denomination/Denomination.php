<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Denomination;
use Magestore\Webpos\Api\Data\Denomination\DenominationInterface;

/**
 * Class Denomination
 * @package Magestore\Webpos\Model\Denomination
 */
class Denomination extends \Magento\Framework\Model\AbstractModel implements \Magestore\Webpos\Api\Data\Denomination\DenominationInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'webpos_denomination';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [])
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\ResourceModel\Denomination\Denomination');
    }
    /**
     *  Get Denomination Id
     * @return string|null
     */
    public function getDenominationId(){
        return $this->getData(self::DENOMINATION_ID);
    }

    /**
     * Set Denomination Id
     *
     * @param string|null $denominationId
     * @return $this
     */
    public function setDenominationId($denominationId){
        return $this->setData(self::DENOMINATION_ID, $denominationId);
    }

    /**
     *  Get Denomination Name
     * @return string
     */
    public function getDenominationName(){
        return $this->getData(self::DENOMINATION_NAME);
    }

    /**
     * Set Denomination Name
     *
     * @param string $denominationName
     * @return $this
     */
    public function setDenominationName($denominationName){
        return $this->setData(self::DENOMINATION_NAME, $denominationName);
    }

    /**
     *  Get Denomination Value
     * @return float
     */
    public function getDenominationValue(){
        return $this->getData(self::DENOMINATION_VALUE);
    }

    /**
     * Set Denomination Value
     *
     * @param float $denominationValue
     * @return $this
     */
    public function setDenominationValue($denominationValue){
        return $this->setData(self::DENOMINATION_VALUE, $denominationValue);
    }

    /**
     *  Get Location Ids
     * @return string
     */
    public function getLocationIds()
    {
        return $this->getData(self::LOCATION_IDS);
    }

    /**
     * Set Location Ids
     *
     * @param string $locationIds
     * @return $this
     */
    public function setLocationIds($locationIds)
    {
        return $this->setData(self::LOCATION_IDS,  $locationIds);
    }

    /**
     *  Get Sort Order
     * @return int|null
     */
    public function getSortOrder(){
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * Set Sort Order
     *
     * @param int|null $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder){
        return $this->setData(self::SORT_ORDER, $sortOrder);
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
        \Magestore\Webpos\Api\Data\Denomination\DenominationExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}