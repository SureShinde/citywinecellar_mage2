<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Block\Adminhtml\Template\Edit\Button;

use Magento\Ui\Component\Control\Container;

/**
 * Class Save
 */
class Delete extends Generic
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $id = $this->getParam('id',false);
        if(!$id){
            return [];
        }
        return [
            'label' => __('Delete'),
            'class' => 'save primary',
            'url' => $this->getUrl('*/*/delete', ['id' => $id])
        ];
    }
}
