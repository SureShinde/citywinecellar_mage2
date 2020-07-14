<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Block\DropshipRequest\Detail;

/**
 * Class Button
 * @package Magestore\DropshipSuccess\Block\DropshipRequest\Detail
 */
class Button extends \Magento\Framework\View\Element\Template
{

    /**
     * cancel dropship request url
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->getUrl('dropship/dropshipRequest/cancelDropship', ['dropship_id' => $this->getRequest()->getParam('dropship_id')]);
    }
}
