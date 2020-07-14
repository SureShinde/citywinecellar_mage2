<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Ui\Component\Listing\Columns;

/**
 * Class Location
 * @package Magestore\ReportSuccess\Ui\Component\Listing\Columns
 */
class Location extends Scroll
{
    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        $data = $this->getData();
        if ($data && $data['config']) {
            if ($this->reportManagement->isMSIEnable()) {
                $data['config']['label'] = __('Source');
                $data['config']['caption'] = __('All Sources');
                $this->setData($data);
            }
        }
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $warehouseNameArray = [];
                if (isset($item[$fieldName])) {
                    $item[$fieldName] = explode(',', $item[$fieldName]);
                    foreach ($item[$fieldName] as $warehouseId) {
                        if ($this->reportManagement->isMSIEnable()) {
                            $sourceModel = $this->objectManager
                                ->create('Magento\InventoryApi\Api\SourceRepositoryInterface')
                                ->get($warehouseId);
                            $warehouseNameArray[] = $sourceModel->getName();
                        } else {
                            /** @var \Magestore\InventorySuccess\Model\Warehouse $warehouseModel */
                            $warehouseModel = $this->objectManager->create('Magestore\InventorySuccess\Model\Warehouse')
                                ->load($warehouseId);
                            $warehouseNameArray[] = $warehouseModel->getWarehouseName();
                        }
                    }
                    sort($warehouseNameArray);
                    $item[$fieldName] = $this->addScrollToField(implode('</br>', $warehouseNameArray));
                }

            }
        }
        return $dataSource;
    }
}
