<?php

namespace Magestore\Storepickup\Model\Config\Source;

/**
 * Class PaymentMethods
 *
 * Used to create payment methods
 */
class PaymentMethods implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @inheritdoc
     */
    protected $_collectionFactory;

    /**
     * PaymentMethods constructor.
     *
     * @param \Magento\Payment\Model\Config $collectoryFactory
     */
    public function __construct(
        \Magento\Payment\Model\Config $collectoryFactory
    ) {
        $this->_collectionFactory = $collectoryFactory;
    }

    /**
     * To option array
     *
     * @return array|void
     */
    public function toOptionArray()
    {
        $storeCollection = $this->_collectionFactory->getActiveMethods();
        if (!count($storeCollection)) {
            return;
        }

        $options = [];

        foreach ($storeCollection as $item) {
            $title = $item->getTitle() ? $item->getTitle() : $item->getCode();
            $options[] = ['value' => $item->getCode(), 'label' => $title];
        }

        return $options;
    }
}
