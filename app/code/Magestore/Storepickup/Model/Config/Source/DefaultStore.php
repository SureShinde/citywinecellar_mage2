<?php

namespace Magestore\Storepickup\Model\Config\Source;

/**
 * Class DefaultStore
 *
 * Used to create default store source
 */
class DefaultStore implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @inheritdoc
     */
    protected $_collectionFactory;

    /**
     * DefaultStore constructor.
     *
     * @param \Magestore\Storepickup\Model\ResourceModel\Store\CollectionFactory $collectoryFactory
     */
    public function __construct(
        \Magestore\Storepickup\Model\ResourceModel\Store\CollectionFactory $collectoryFactory
    ) {
        $this->_collectionFactory = $collectoryFactory;
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $storeCollection = $this->_collectionFactory->create();
        $storeCollection = $storeCollection->addFieldToFilter('status', '1');
        $arr = [];
        if ($storeCollection->count() == '1') {
            foreach ($storeCollection as $item) {
                $arr[] = ['value' => $item->getId(), 'label' => $item->getStoreName()];
            }
            return $arr;
        }

        $arr [] = ['value' => 0, 'label' => '---Choose Default Store---'];
        foreach ($storeCollection as $item) {
            $arr[] = ['value' => $item->getId(), 'label' => $item->getStoreName()];
        }
        return $arr;
    }
}
