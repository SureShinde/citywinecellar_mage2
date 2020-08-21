<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Block\Adminhtml\Staff\Role\Edit;

/**
 * Edit role Tabs
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Construct
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('webpos_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Role Information'));
    }

    /**
     * Before To Html
     *
     * @return $this
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'webpos_form',
            [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()->createBlock(
                    \Magestore\Appadmin\Block\Adminhtml\Staff\Role\Edit\Tab\Form::class
                )->toHtml(),
                'active' => true
            ]
        );
        $permissionBlock = $this->getLayout()->createBlock(
            \Magestore\Appadmin\Block\Adminhtml\Staff\Role\Edit\Tab\Permission::class
        );
        $permissionBlock->addChild(
            'block_discount_percent',
            \Magestore\Appadmin\Block\Adminhtml\Staff\Role\Edit\Tab\Permission\Discount::class
        );
        $this->addTab(
            'webpos_permission',
            [
                'label' => __('Permission'),
                'title' => __('Permission'),
                'content' => $permissionBlock->toHtml()
            ]
        );

        $this->addTab(
            'user_section',
            [
                'label' => __('Staff List'),
                'title' => __('Staff List'),
                'class' => 'ajax',
                'url' => $this->getUrl(
                    '*/*/staff',
                    ['_current' => true, 'id' => $this->getRequest()->getParam('id')]
                )
            ]
        );

        return parent::_beforeToHtml();
    }
}
