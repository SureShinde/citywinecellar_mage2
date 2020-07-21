<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model\ResourceModel\Rewards\Collection;

use Magento\Customer\Controller\RegistryConstants as RegistryConstants;

class Grid extends \Amasty\Rewards\Model\ResourceModel\Rewards\Collection
{

    /**
     * Initialize db select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $this->addCustomerIdFilter(
            $this->_registryManager->registry(RegistryConstants::CURRENT_CUSTOMER_ID)
        );
        $this->addOrder('id', 'DESC');
        parent::_initSelect();
        return $this;
    }
}
