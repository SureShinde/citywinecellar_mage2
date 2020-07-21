<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model\Config\Backend;

class Round extends \Magento\Framework\App\Config\Value
{
    /**
     * @return \Magento\Framework\App\Config\Value|void
     */
    public function beforeSave()
    {
        $this->setValue((float)$this->getValue());

        parent::beforeSave();
    }
}
