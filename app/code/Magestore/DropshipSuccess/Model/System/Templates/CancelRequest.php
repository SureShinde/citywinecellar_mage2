<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Model\System\Templates;

/**
 * System template CancelRequest
 */
class CancelRequest
{
    /**
     * @var \Magento\Email\Model\ResourceModel\Template\CollectionFactory
     */
    protected $_emailCollection;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_codeRegistry;

    /**
     * CancelRequest constructor.
     *
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Email\Model\ResourceModel\Template\CollectionFactory $collection
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Email\Model\ResourceModel\Template\CollectionFactory $collection
    ) {
        $this->_codeRegistry = $registry;
        $this->_emailCollection = $collection;
    }

    /**
     * To Option Array
     *
     * @return mixed
     */
    public function toOptionArray()
    {
        if (!$collection = $this->_codeRegistry->registry('config_system_email_template')) {
            $collection = $this->_emailCollection->create()->load();
            $this->_codeRegistry->register('config_system_email_template', $collection);
        }
        $options = $collection->toOptionArray();
        array_unshift(
            $options,
            [
                'value' => 'magestore_cancel_request_to_supplier',
                'label' => __('Cancel request notice to supplier (Default)')
            ],
            [
                'value' => '0',
                'label' => __('None')
            ]
        );

        return $options;
    }
}
