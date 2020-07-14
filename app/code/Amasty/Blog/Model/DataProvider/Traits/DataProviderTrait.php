<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\DataProvider\Traits;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;

trait DataProviderTrait
{
    /**
     * @param object $current
     * @param int $storeId
     * @param array $data
     * @return array
     */
    public function prepareData($current, $storeId, $data)
    {
        if ($current && $current->getId()) {
            $data[$current->getId()] = $current->getData();
            $this->addDataByStore($data, $storeId, $current->getId());
        }

        return $data;
    }
    /**
     * @param array $data
     * @param int $storeId
     * @param int $currentEntityId
     */
    public function addDataByStore(&$data, $storeId, $currentEntityId)
    {
        if ($storeId) {
            $data[$currentEntityId]['store_id'] = $storeId;
            $item = $this->repository->getByIdAndStore($currentEntityId, $storeId);
            if ($item) {
                foreach ($this->getFieldsByStore() as $fieldSet) {
                    foreach ($fieldSet as $field) {
                        if ($item->getData($field) !== null) {
                            $data[$currentEntityId][$field] = $item->getData($field);
                        }
                    }
                }
            }
        }
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }
}
