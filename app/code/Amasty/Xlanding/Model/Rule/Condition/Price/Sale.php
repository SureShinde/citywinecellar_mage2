<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */

namespace Amasty\Xlanding\Model\Rule\Condition\Price;

class Sale extends AbstractPrice
{
    protected $_inputType = 'select';

    public function getAttributeElementHtml()
    {
        return __('Is on Sale');
    }

    protected function _getAttributeCode()
    {
        return 'sale';
    }

    public function getInputType()
   {
       return 'select';
   }

   public function getValueElementType()
   {
       return 'select';
   }

    protected function _prepareValueOptions()
    {
        $selectReady = $this->getData('value_select_options');
        $hashedReady = $this->getData('value_option');

        $selectOptions = [
            ['value' => 1, 'label' => 'Yes'],
            ['value' => 0, 'label' => 'No']
        ];

        $this->_setSelectOptions($selectOptions, $selectReady, $hashedReady);

        return $this;
    }

    protected function _getCondition()
    {
        if (!$this->_condition) {
            $alias = $this->_getAlias();

            $value     = $this->getValue();
            $operator  = $this->getOperatorForValidate();

            if ($value && $operator == '=='){
                $this->_condition = 'ifnull(catalog_rule.rule_price, ' . $alias . '.final_price) < ' . $alias . '.price';
            } else {
                $this->_condition = 'ifnull(catalog_rule.rule_price, ' . $alias . '.final_price) >= ' . $alias . '.price';
            }

            $this->_condition .= ' AND (catalog_rule.latest_start_date < NOW() OR catalog_rule.latest_start_date IS NULL)
                                AND (catalog_rule.earliest_end_date > NOW() OR catalog_rule.earliest_end_date IS NULL)';
        }
        return $this->_condition;
    }
}