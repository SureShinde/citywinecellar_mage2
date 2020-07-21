<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Block\Adminhtml\Rewards\Widget\Grid\Renderer;

use Amasty\Rewards\Helper\Data;
use Magento\Backend\Block\Context;

class Actions extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Input
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
        $action = $row->getData('action');

        $actions = $this->helper->getActions();

        if (isset($actions[$action])) {
            $result = $actions[$action];
        } else {
            $result = $action;
        }

        return $result;
    }
}
