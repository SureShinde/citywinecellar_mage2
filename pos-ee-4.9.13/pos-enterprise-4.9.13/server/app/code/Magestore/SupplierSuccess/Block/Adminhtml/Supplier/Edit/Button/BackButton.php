<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Block\Adminhtml\Supplier\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class BackButton implements ButtonProviderInterface
{
    /**
     * @return array
     * @codeCoverageIgnore
     */

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $context = \Magento\Framework\App\ObjectManager::getInstance()->create(
            'Magento\Framework\View\Element\UiComponent\Context'
        );
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $context->getUrl('*/*/')),
            'class' => 'back',
            'sort_order' => 10
        ];
    }
}
