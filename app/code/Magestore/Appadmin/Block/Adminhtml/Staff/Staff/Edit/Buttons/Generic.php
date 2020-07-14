<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Block\Adminhtml\Staff\Staff\Edit\Buttons;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magestore\Appadmin\Api\Data\Staff\StaffInterface;
use Magestore\Appadmin\Model\Staff\Staff;

/**
 * Class Generic
 */
class Generic implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var Context
     */
    protected $context;

    /**
     * Registry
     *
     * @var Registry
     */
    protected $registry;

    /**
     * @var StaffInterface
     */
    protected $staffInterface;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;

    /**
     * Generic constructor.
     * @param Context $context
     * @param Registry $registry
     * @param StaffInterface $staffInterface
     * @param \Magento\Framework\AuthorizationInterface $authorizationInterface
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StaffInterface $staffInterface,
        \Magento\Framework\AuthorizationInterface $authorization
    ) {
        $this->context = $context;
        $this->registry = $registry;
        $this->staffInterface = $staffInterface;
        $this->authorization = $authorization;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrl($route, $params);
    }

    /**
     * Get product
     *
     * @return Staff
     */
    public function getStaff()
    {
        return $this->registry->registry('current_staff');
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [];
    }
}
