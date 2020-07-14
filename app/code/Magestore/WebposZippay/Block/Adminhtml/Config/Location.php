<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposZippay\Block\Adminhtml\Config;


class Location extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{

    protected $_template = 'Magestore_WebposZippay::config/array.phtml';
    protected $_webposLocationRenderer;


    public function _getWebposLocationRenderer()
    {
        if (!$this->_webposLocationRenderer) {
            $this->_webposLocationRenderer = $this->getLayout()->createBlock(
                \Magestore\WebposZippay\Block\Adminhtml\Config\WebposLocation::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_webposLocationRenderer;
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'webpos_location',
            [
                'label' => __('Location'),
                'style' => 'width:200px',
                'renderer' => $this->_getWebposLocationRenderer()
            ]
        );
        $this->addColumn(
            'location_id',
            [
                'label' => __('Location Id'),
                'style' => 'width:120px'
            ]
        );


        $this->_addAfter = false;
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->_getWebposLocationRenderer()->calcOptionHash($row->getData('webpos_location'))] =
            'selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }
}