<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */
namespace Magestore\ReportSuccess\Model;

/**
 * Class Bookmark
 * @package Magestore\ReportSuccess\Model
 */
class Bookmark extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Bookmark constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Bookmark $resource
     * @param ResourceModel\Bookmark\Collection $resourceCollection
     * @param \Magento\Authorization\Model\UserContextInterface $userContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\ReportSuccess\Model\ResourceModel\Bookmark $resource,
        \Magestore\ReportSuccess\Model\ResourceModel\Bookmark\Collection $resourceCollection,
        \Magento\Authorization\Model\UserContextInterface $userContext,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        // Load current user bookmark
        $resource->load($this, $userContext->getUserId(), 'user_id');
        if (!$this->getId()) {
            $this->setData('user_id', $userContext->getUserId());
        } elseif (!$this->hasData('config_arr')) {
            $this->afterLoad();
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function afterLoad()
    {
        if ($this->_getData('config')) {
            $this->setData('config_arr', json_decode($this->_getData('config'), true));
        } else {
            $this->setData('config_arr', []);
        }
        return parent::afterLoad();
    }

    /**
     * {@inheritDoc}
     */
    public function beforeSave()
    {
        if ($this->_getData('config_arr')) {
            $this->setData('config', json_encode($this->_getData('config_arr')));
        }
        return parent::beforeSave();
    }

    /**
     * @return $this
     * @throws \Exception
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function saveBookmark()
    {
        $this->_resource->save($this);
        return $this;
    }
    
    /**
     * Get locations
     * 
     * @return array
     */
    public function getLocations()
    {
        if ($locations = $this->getData('config_arr/locations')) {
            return $locations;
        }
        return [' '];
    }

    /**
     * Set locations
     *
     * @param $locations
     * @return $this
     */
    public function setLocations($locations)
    {
        $config = $this->_getData('config_arr');
        if (!$config) {
            $config = [
                'locations' => $locations,
            ];
        } else {
            $config['locations'] = $locations;
        }
        $this->setData('config_arr', $config);
        return $this;
    }
    
    /**
     * Get metric
     * 
     * @return string
     */
    public function getMetric()
    {
        if ($metric = $this->getData('config_arr/metric')) {
            return $metric;
        }
        return \Magestore\ReportSuccess\Model\Source\Adminhtml\StockByLocation\Metric::QTY_ON_HAND;
    }

    /**
     * Set metric
     *
     * @param $metric
     * @return $this
     */
    public function setMetric($metric)
    {
        $config = $this->_getData('config_arr');
        if (!$config) {
            $config = [
                'metric' => $metric,
            ];
        } else {
            $config['metric'] = $metric;
        }
        $this->setData('config_arr', $config);
        return $this;
    }
}
