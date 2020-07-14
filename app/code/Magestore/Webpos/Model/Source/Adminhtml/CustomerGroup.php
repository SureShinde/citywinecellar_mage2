<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * class \Magestore\Webpos\Model\Source\Adminhtml\CustomerGroup
 * 
 * Web POS CustomerGroup source model
 * Use to get magento customer group
 * Methods:
 *  filterCustomerCollection
 *  getAllowCustomerGroups
 *  getOptionArray
 *  toOptionArray
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
/**
 * Class CustomerGroup
 * @package Magestore\Webpos\Model\Source\Adminhtml
 */
class CustomerGroup implements \Magento\Framework\Option\ArrayInterface {
    /**
     *
     */
    const ALL = 'all';
    /**
     *
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory 
     */
    protected $_customerGroupCollectionFactory;
    
    /**
     * 
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupCollectionFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupCollectionFactory
    ) {
        $this->_customerGroupCollectionFactory = $customerGroupCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $groups = $this->_customerGroupCollectionFactory->create();
        $options = [];
        $options[] = array(
            'value' => self::ALL,
            'label' => __('All groups')
        );
        foreach ($groups as $group) {
            if ($group->getId() == 0)
                continue;
            $options[] = array(
                'value' => $group->getId(),
                'label' =>$group->getData('customer_group_code')
            );

        }
        return $options;
    }

    /**
     * @return array
     */
    public function getOptionArray()
    {
        $array = array(self::ALL => __('All groups'));
        $groups = $this->_customerGroupCollectionFactory->create();
        foreach ($groups as $group) {
            if ($group->getId() == 0)
                continue;
            $array[$group->getId()] = $group->getData('customer_group_code');
        }
        return $array;
    }

    
    /**
     * 
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $collection
     * @return \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    public function filterCustomerCollection($collection) {
        try {
            if ($collection) {
                $customer_group = $this->getAllowCustomerGroups();
                if (count($customer_group) > 0 && !in_array(self::ALL, $customer_group)) {
                    $collection->addAttributeToFilter(array(array('attribute' => 'group_id', 'in' => $customer_group)));
                }
            }
            return $collection;
        } catch (\Exception $e) {
            return $collection;
        }
    }
}
