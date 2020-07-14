<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\Form\Field;

use Magestore\BarcodeSuccess\Block\Adminhtml\Template\Description;

class ShippingMethod extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var StatusField
     */
    protected $_statusRenderer;

    /**
     * @var DescriptionField
     */
    protected $_description;

    /**
     * @var bool
     */
    protected $_hasDescription = false;

    /**
     * Retrieve status column renderer
     *
     * @return Status
     */
    public function _getStatusRenderer()
    {
        if (!$this->_statusRenderer) {
            $this->_statusRenderer = $this->getLayout()->createBlock(
                'Magestore\PurchaseOrderSuccess\Block\Adminhtml\Form\Field\Status',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_statusRenderer;
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface|DescriptionField
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _getDescriptionRenderer(){
        if (!$this->_description) {
            $this->_description = $this->getLayout()->createBlock(
                'Magestore\PurchaseOrderSuccess\Block\Adminhtml\View\Element\Html\Textarea'
            );
        }
        return $this->_description;
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    public function _prepareToRender()
    {
        $this->addColumn('name', ['label' => __('Name')]);
        if($this->_hasDescription)
            $this->addColumn(
                'description',
                ['label' => __('Description'), 'renderer' => $this->_getDescriptionRenderer()]
            );
        $this->addColumn(
            'status',
            ['label' => __('Status'), 'renderer' => $this->_getStatusRenderer()]
        );
        $this->_addAfter = false;
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     */
    public function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->_getStatusRenderer()->calcOptionHash($row->getData('status'))] =
            'selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }
}