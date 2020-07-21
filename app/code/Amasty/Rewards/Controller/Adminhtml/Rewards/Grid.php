<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Controller\Adminhtml\Rewards;

use Magento\Customer\Controller\RegistryConstants;

class Grid extends \Amasty\Rewards\Controller\Adminhtml\Rewards
{
    const ALLOWED_HANDLES = [
        'rewards' => 'amasty_rewards_rewards_ajaxgrid',
        'expiration' => 'amasty_rewards_expiration_ajaxgrid'
    ];

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
    }

    public function execute()
    {
        $handleName = $this->getRequest()->getParam('name');

        if (!empty(self::ALLOWED_HANDLES[$handleName])) {
            $this->_view->getLayout()->getUpdate()->addHandle(self::ALLOWED_HANDLES[$handleName]);
        }

        $customerId = (int)$this->getRequest()->getParam('id');

        if ($customerId) {
            $this->coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customerId);
        }

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
