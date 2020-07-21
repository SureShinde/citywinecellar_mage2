<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

namespace Amasty\Rewards\Model\ResourceModel\Rule\Collection;

class Grid extends \Amasty\Rewards\Model\ResourceModel\Rule\Collection
{
    /**
     * Initialize db select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();
        return $this;
    }
}
