<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Rewards\Block\Adminhtml\Rule\Grid\Renderer;

use Amasty\Rewards\Helper\Data;
use Magento\Backend\Block\Context;

class Groups extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Input
{
    /**
     * @var Data
     */
    private $helper;

    public function __construct(
        Data $helper,
        Context $context,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        $groups = $row->getData('cust_groups');
        if (!$groups) {
            return __('Restricts For All');
        }
        $groups = explode(',', $groups);

        $html = '';
        foreach ($this->helper->getAllGroups() as $row) {
            if (in_array($row['value'], $groups)) {
                $html .= $row['label'] . "<br />";
            }
        }
        return $html;
    }
}
