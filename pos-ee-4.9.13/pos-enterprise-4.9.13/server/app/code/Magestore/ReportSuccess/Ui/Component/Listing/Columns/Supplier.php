<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Ui\Component\Listing\Columns;

/**
 * Class Supplier
 * @package Magestore\ReportSuccess\Ui\Component\Listing\Columns
 */
class Supplier extends Scroll
{

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        if (!$this->moduleManager->isEnabled('Magestore_SupplierSuccess')){
            $this->_data['config']['componentDisabled'] = true;
        } else {
            parent::prepare();
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
        $filterParam = $this->context->getFilterParam('supplier');
        if ($this->moduleManager->isEnabled('Magestore_SupplierSuccess')) {
            if (isset($dataSource['data']['items'])) {
                $fieldName = $this->getData('name');
                foreach ($dataSource['data']['items'] as & $item) {

                    $supplierArray = [];
                    if (isset($item[$fieldName])) {
                        $item[$fieldName] = explode(',', $item[$fieldName]);
                        foreach ($item[$fieldName] as $supplierId) {
                            $supplierModel = $this->objectManager->create('Magestore\SupplierSuccess\Model\Supplier')->load($supplierId);
                            if (!$filterParam || ($filterParam && $supplierId == $filterParam)) {
                                $supplierArray[] = $supplierModel->getSupplierName();
                            }
                        }
                        sort($supplierArray);
                        $item[$fieldName] = $this->addScrollToField(implode('</br>', $supplierArray));
                    }

                }
            }
        }
        return $dataSource;
    }
}
