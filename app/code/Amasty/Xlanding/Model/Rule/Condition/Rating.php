<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\Rule\Condition;

class Rating extends AbstractCondition
{
    protected $_inputType = 'select';

    const CONVERT_IN_HUNDRED = 20;

    public function getAttributeElementHtml()
    {
        return __('Rating by \'rating_summary\' attribute');
    }

    public function getInputType()
    {
        return 'numeric';
    }

    public function getValueElementType()
    {
        return 'select';
    }

    protected function _getAttributeCode()
    {
        return 'rating_summary';
    }

    protected function _prepareValueOptions()
    {
        $selectReady = $this->getData('value_select_options');
        $hashedReady = $this->getData('value_option');

        $selectOptions = [
            ['value' => 0, 'label' => '0'],
            ['value' => 1, 'label' => '1'],
            ['value' => 2, 'label' => '2'],
            ['value' => 3, 'label' => '3'],
            ['value' => 4, 'label' => '4'],
            ['value' => 5, 'label' => '5'],
        ];

        $this->_setSelectOptions($selectOptions, $selectReady, $hashedReady);

        return $this;
    }

    public function collectValidatedAttributes($select)
    {
        $alias = $this->_getAlias();
        $value     = $this->getValue();
        $operator  = $this->getOperatorForValidate();

        $mapTpl = '(%1$s.`entity_pk_value` = e.entity_id) 
            AND (%1$s.entity_type = 1) 
            AND (%1$s.store_id = 1)'; /** @var todo store 0 or current */

        $this->_condition = $this->getOperatorCondition(
            $alias . '.rating_summary',
            $operator,
            $value * self::CONVERT_IN_HUNDRED
        );

        $select->joinLeft(
            [
                $alias => $this->_productResource->getTable('review_entity_summary')
            ],
            sprintf($mapTpl, $alias),
            []
        );
    }
}
