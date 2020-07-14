<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Ui\Component\Listing\Columns;

/**
 * Class PurchaseOrder
 * @package Magestore\ReportSuccess\Ui\Component\Listing\Columns
 */
class PurchaseOrder extends Scroll
{

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        if (!$this->moduleManager->isEnabled('Magestore_PurchaseOrderSuccess')){
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
        if ($this->moduleManager->isEnabled('Magestore_PurchaseOrderSuccess')) {
            if (isset($dataSource['data']['items'])) {
                $fieldName = $this->getData('name');
                foreach ($dataSource['data']['items'] as & $item) {

                    $poArray = [];
                    if ($item[$fieldName]) {
                        $item[$fieldName] = explode(',', $item[$fieldName]);
                        foreach ($item[$fieldName] as $poId) {
                            $poModel = $this->objectManager->create('Magestore\PurchaseOrderSuccess\Model\PurchaseOrder')->load($poId);
                            $poArray[] = '<a href="'.$this->objectManager->get('Magento\Backend\Model\UrlInterface')->getUrl('purchaseordersuccess/purchaseOrder/view', ['id'=>$poModel->getData('purchase_order_id')]) .'">'.
                                $poModel->getData('purchase_code')
                                .'</a>';
                        }
                        $item[$fieldName] = $this->addScrollToField(implode('</br>', $poArray));
                    }

                }
            }
        }
        return $dataSource;
    }
}
