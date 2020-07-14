<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\ResourceModel\Traits;

use Magento\Framework\Model\AbstractModel;

trait ResourceModelTrait
{
    /**
     * @param $object
     */
    public function addDefaultStoreSelect($object)
    {
        $connection = $this->getConnection();
        $idFieldName = $this->getIdFieldName();
        $select = $connection->select()->from(
            $this->getTable(self::STORE_TABLE_NAME),
            ['*']
        )->where(sprintf('%s = :%s && store_id = 0', $idFieldName, $idFieldName));

        $storesData = $connection->fetchRow($select, [':' . $idFieldName => $object->getId()]);

        if ($storesData) {
            $object->addData($storesData);
        }
    }

    /**
     * @param AbstractModel $object
     */
    private function saveStoreData($object)
    {
        $connection = $this->getConnection();

        $storeId = $object->getStoreId() ?: 0;
        $condition = [$this->getIdFieldName() . ' = ?' => $object->getId(), 'store_id = ?' => $storeId];
        $connection->delete($this->getTable(self::STORE_TABLE_NAME), $condition);

        $valuesForSave = $this->prepareStoreData($object);

        $connection->insert($this->getTable(self::STORE_TABLE_NAME), $valuesForSave);
    }

    /**
     * @param AbstractModel $object
     * @return array
     */
    private function prepareStoreData($object)
    {
        $valuesForSave = [];
        foreach (self::STORE_TABLE_FIELDS as $value) {
            if ($value == 'store_id') {
                $valuesForSave[$value] = $object->getData($value) ?: 0;
            } else {
                $valuesForSave[$value] = $object->getData($value);
            }
        }

        return $valuesForSave;
    }
}
