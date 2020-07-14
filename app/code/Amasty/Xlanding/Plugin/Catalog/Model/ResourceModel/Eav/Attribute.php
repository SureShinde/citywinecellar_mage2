<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Plugin\Catalog\Model\ResourceModel\Eav;

use Amasty\Base\Model\Serializer;
use \Magento\Catalog\Model\ResourceModel\Eav\Attribute as MagentoAttribute;

class Attribute
{
    const CONDITION_TYPE_COMBINE = 'Amasty\Xlanding\Model\Rule\Condition\Combine';
    const CONDITION_TYPE_PRODUCT = 'Amasty\Xlanding\Model\Rule\Condition\Product';

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var \Amasty\Xlanding\Model\ResourceModel\Page\CollectionFactory
     */
    private $pageCollectionFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var MagentoAttribute
     */
    private $attribute;

    public function __construct(
        \Amasty\Base\Model\Serializer $serializer,
        \Amasty\Xlanding\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->serializer = $serializer;
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @param MagentoAttribute $attribute
     * @return array
     */
    public function afterDelete(MagentoAttribute $attribute, $result)
    {
        $this->attribute = $attribute;
        $attributeCode = $attribute->getAttributeCode();
        $collection = $this->pageCollectionFactory->create();
        $collection->addFieldToFilter('conditions_serialized', ['like' => "%" . $attributeCode . "%"]);
        foreach ($collection as $page) {
            $conditions = $this->serializer->unserialize($page->getData('conditions_serialized'));
            if(isset($conditions['conditions']) && !empty($conditions['conditions'])) {
                foreach ($conditions['conditions'] as $key => &$condition) {
                    if (!$this->fixConditionsRecursive($condition)) {
                        unset($conditions['conditions'][$key]);
                    }
                }
                $page->setData('conditions_serialized', $this->serializer->serialize($conditions));
            }
        }
        $collection->save();
        return $result;
    }

    /**
     * @param $condition
     * @return bool
     */
    private function fixConditionsRecursive(&$condition)
    {
        if (isset($condition['type'])
            && $condition['type'] == self::CONDITION_TYPE_COMBINE
            && isset($condition['conditions'])
        ) {
            foreach ($condition['conditions'] as $key => &$childCondition) {
                if (!$this->fixConditionsRecursive($childCondition)) {
                    unset($condition['conditions'][$key]);
                }
            }
            if (empty($condition['conditions'])) {
                return false;
            }
        } elseif ($condition['type'] == self::CONDITION_TYPE_PRODUCT) {
            if (isset($condition['attribute'])
                && !empty($condition['attribute'])
                && $condition['attribute'] !== 'null'
            ) {
               return $this->validateAttribute($condition['attribute']);
            }
        }
        return true;
    }

    /**
     * @param $attributeCode
     * @return bool
     */
    private function validateAttribute($attributeCode)
    {
        return $attributeCode !== $this->attribute->getAttributeCode();
    }
}
