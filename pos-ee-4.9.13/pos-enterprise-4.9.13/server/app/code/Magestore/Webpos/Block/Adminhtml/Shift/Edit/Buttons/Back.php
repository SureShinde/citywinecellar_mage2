<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Shift\Edit\Buttons;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Back implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * Back constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }
    /**
     * @return array
     */
    public function getButtonData()
    {
        $posId = $this->context->getRequestParam('pos_id');
        if($posId) {
            $url = $this->context->getUrl('*/pos/edit', ['id' => $posId]);
        } else {
            $url = $this->context->getUrl('*/pos/index');
        }
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $url),
            'class' => 'back',
            'sort_order' => 10
        ];
    }
}