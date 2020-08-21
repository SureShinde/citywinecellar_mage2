<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\AdjustStock\Block\Adminhtml\AdjustStock\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class Back
 *
 * Button back block
 */
class Back extends \Magestore\AdjustStock\Block\Adminhtml\AdjustStock\AbstractAdjustStock implements
    ButtonProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('*/*/', ['_secure' => true])),
            'class' => 'back',
            'sort_order' => 10
        ];
    }
}
