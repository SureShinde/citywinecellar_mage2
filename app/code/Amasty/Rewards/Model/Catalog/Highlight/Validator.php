<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model\Catalog\Highlight;

class Validator
{
    const ALLOWED_CUSTOMER_CONDITIONS = [
        \Amasty\Conditions\Model\Rule\Condition\CustomerAttributes::class
    ];

    const ALLOWED_PRODUCT_CONDITIONS = [
        \Magento\SalesRule\Model\Rule\Condition\Product\Found::class,
    ];
    /**
     * @param \Amasty\Rewards\Api\Data\RuleInterface $rule
     *
     * @param ValidObject $validObject
     *
     * @return bool
     */
    public function validate($rule, $validObject)
    {
        $conditions = $rule->getConditions()->getConditions();
        $all = $rule->getConditions()->getAggregator() === 'all';
        $true = (bool)$rule->getConditions()->getValue();

        /** @var \Magento\Rule\Model\Condition\AbstractCondition $condition */
        foreach ($conditions as $condition) {
            if ($entity = $this->getEntityByCondition($condition->getType(), $validObject)) {
                $validated = $condition->validate($entity);

                if ($all && $validated !== $true) {
                    return false;
                } elseif (!$all && $validated === $true) {
                    return true;
                }
            }
        }

        return $all ? true : false;
    }

    /**
     * Return entity for validation due to condition type
     *
     * @param string $conditionType
     * @param ValidObject $validObject
     *
     * @return ValidObject|\Magento\Customer\Model\Customer|bool
     */
    private function getEntityByCondition($conditionType, $validObject)
    {
        $entity = false;

        if (in_array($conditionType, self::ALLOWED_CUSTOMER_CONDITIONS)) {
            $entity = $validObject->getCustomer();
        } elseif (in_array($conditionType, self::ALLOWED_PRODUCT_CONDITIONS)) {
            $entity = $validObject;
        }

        return $entity;
    }
}
