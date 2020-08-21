<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Rewardpoints\Ui\Component\Listing\Column\Transaction;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Transaction store view options
 */
class StoreView implements OptionSourceInterface
{
    protected $_array;
    /**
     * @var \Magento\Store\Model\ResourceModel\Store\CollectionFactory
     */
    protected $_storesFactory;
    /**
     * @param \Magento\Store\Model\ResourceModel\Store\CollectionFactory $storesFactory
     */
    public function __construct(\Magento\Store\Model\ResourceModel\Store\CollectionFactory $storesFactory)
    {
        $this->_storesFactory = $storesFactory;
    }

    /**
     * To Option Hash
     *
     * @return array
     */
    public function toOptionHash()
    {
        if (!$this->_array) {
            /* @var \Magento\Store\Model\ResourceModel\Store\Collection $stores */
            $stores = $this->_storesFactory->create();
            $this->_array = $stores->load()->toOptionHash();
        }
        return $this->_array;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $options = [];
        foreach (self::toOptionHash() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $options;
    }
}
