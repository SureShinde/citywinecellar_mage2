<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Block\Adminhtml\System\Config;

/**
 * Class Tag
 * @package Magestore\OrderSuccess\Block\Adminhtml\System\Config
 */
class Tag extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{

    /**
     * Rows cache
     *
     * @var array|null
     */
    private $_arrayRowsCache;

    /**
     * @var string
     */
    protected $_template = 'Magestore_OrderSuccess::system/config/form/field/array.phtml';

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->addColumn('title', [
            'label' => __('Title'),
            'style' => 'width:250px',
        ]);
        $this->addColumn('color', [
            'label' => __('Color'),
            'class' => 'jscolor',
            'style' => 'width:250px',
        ]);
        $this->_addAfter = FALSE;
        $this->_addButtonLabel = __('Add Tag');

        parent::_construct();
    }

    public function getArrayRows()
    {
        if (null !== $this->_arrayRowsCache) {
            return $this->_arrayRowsCache;
        }
        $result = [];
        /** @var \Magento\Framework\Data\Form\Element\AbstractElement */
        $element = $this->getElement();
        if($element->getValue() && is_array($element->getValue())){
            $values = $element->getValue();
        }else{
            $values = unserialize($element->getValue());
        }
        if ($values && is_array($values)) {
            foreach ($values as $rowId => $row) {
                $rowColumnValues = [];
                foreach ($row as $key => $value) {
                    $row[$key] = $value;
                    $rowColumnValues[$this->_getCellInputElementId($rowId, $key)] = $row[$key];
                }
                $row['_id'] = $rowId;
                $row['column_values'] = $rowColumnValues;
                $result[$rowId] = new \Magento\Framework\DataObject($row);
                $this->_prepareArrayRow($result[$rowId]);
            }
        }
        $this->_arrayRowsCache = $result;
        return $this->_arrayRowsCache;
    }
}