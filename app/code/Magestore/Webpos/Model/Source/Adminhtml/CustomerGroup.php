<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * Source option CustomerGroup
 */
class CustomerGroup implements \Magento\Framework\Option\ArrayInterface
{

    const ALL = 'all';
    /**
     *
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $_customerGroupCollectionFactory;

    /**
     * CustomerGroup constructor.
     *
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupCollectionFactory
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupCollectionFactory
    ) {
        $this->_customerGroupCollectionFactory = $customerGroupCollectionFactory;
    }

    /**
     * ToOptionArray
     *
     * @return array
     */
    public function toOptionArray()
    {
        $groups = $this->_customerGroupCollectionFactory->create();
        $options = [];
        $options[] = [
            'value' => self::ALL,
            'label' => __('All groups')
        ];
        foreach ($groups as $group) {
            if ($group->getId() == 0) {
                continue;
            }
            $options[] = [
                'value' => $group->getId(),
                'label' => $group->getData('customer_group_code')
            ];
        }
        return $options;
    }

    /**
     * Get Option Array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $array = [self::ALL => __('All groups')];
        $groups = $this->_customerGroupCollectionFactory->create();
        foreach ($groups as $group) {
            if ($group->getId() == 0) {
                continue;
            }
            $array[$group->getId()] = $group->getData('customer_group_code');
        }
        return $array;
    }
}
