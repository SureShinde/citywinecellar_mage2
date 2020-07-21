<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Block\Adminhtml\Rewards\Widget\Grid\Renderer;

class Amount extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Input
{
    public function render(\Magento\Framework\DataObject $row)
    {
        $amount = $row->getData('amount');

        if ($amount > 0) {
            $amount = '+' . $amount;
        }

        return $amount;
    }
}
