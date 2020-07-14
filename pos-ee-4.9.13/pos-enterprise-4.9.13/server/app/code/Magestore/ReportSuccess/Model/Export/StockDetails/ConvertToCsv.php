<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Export\StockDetails;

/**
 * Class ConvertToCsv
 * @package Magestore\ReportSuccess\Model\Export\StockDetails
 */
/**
 * Class ConvertToCsv
 * @package Magestore\ReportSuccess\Model\Export\StockDetails
 */
class ConvertToCsv extends \Magestore\ReportSuccess\Model\Export\AbstractConvertToCsv
{
    /**
     * @var string
     */
    protected $reportTitle = 'STOCK DETAILS REPORT';
    /**
     * @var array
     */
    protected $totalData = [
        'qty_on_hand' => 0,
        'available_qty' => 0,
        'qty_to_ship' => 0,
        'incoming_qty' => 0
    ];
    /**
     * @var array
     */
    protected $needExplodeItem = ['warehouse', 'supplier'];
    /**
     * @var array
     */
    protected $decimalField = ['qty_on_hand','available_qty','qty_to_ship','incoming_qty'];

    /**
     * Abstract function
     * Initialize options
     */
    public function setOptions() {
        $options = $this->metadataProvider->getOptions();
        $options['supplier'] = $this->supplier->toOptionListArray();
        $options['warehouse'] = $this->warehouse->toOptionListArray();
        $filters = $this->_request->getParam('filters');
        if (isset($filters['supplier'])&&count($filters['supplier'])&&isset($options['supplier'][$filters['supplier']])){
            $options['supplier'] = [$filters['supplier']=>$options['supplier'][$filters['supplier']]];
        }
        $this->options = $options;
    }

    /**
     * @param $component
     * @return array
     */
    public function getFields($component){
        $fields = $this->metadataProvider->getFields($component);
        $currentFields = $this->getFilterColumn();
        $isEnableBarcode = $this->scopeConfig->getValue('reportsuccess/general/enable_barcode_in_report', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $barcode = $this->scopeConfig->getValue('reportsuccess/general/barcode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$isEnableBarcode){
            foreach ($currentFields as $key => $value){
                if($value == 'barcode'){
                    unset($currentFields[$key]);
                }
            }
        } else {
            // remove barcode column if barcode attribute is SKU
            if($barcode == 'sku') {
                $keyBarcode = -1;
                foreach ($currentFields as $key => $value){
                    if($value == 'barcode') {
                        $keyBarcode = $key;
                    }
                }
                if($keyBarcode != -1) {
                    unset($currentFields[$keyBarcode]);
                }
            }
        }
        if (!$this->moduleManager->isEnabled('Magestore_PurchaseOrderSuccess')) {
            foreach ($currentFields as $key => $value){
                if(in_array($value, ['incoming_qty', 'supplier'])){
                    unset($currentFields[$key]);
                }
            }
        }
        return count($currentFields)?$currentFields:$fields;
    }

}