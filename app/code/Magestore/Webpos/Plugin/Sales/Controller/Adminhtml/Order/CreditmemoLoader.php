<?php

namespace Magestore\Webpos\Plugin\Sales\Controller\Adminhtml\Order;

/**
 * Class CreditmemoLoader
 *
 * @package Magestore\Webpos\Plugin\Sales\Controller\Adminhtml\Order
 */
class CreditmemoLoader
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * CreditmemoLoader constructor.
     *
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * Before load
     *
     * @param \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $subject
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeLoad(
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $subject
    ) {
        $this->registry->unregister('current_creditmemo');
    }
}
