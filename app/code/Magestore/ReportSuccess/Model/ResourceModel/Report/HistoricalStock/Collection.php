<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */
namespace Magestore\ReportSuccess\Model\ResourceModel\Report\HistoricalStock;

class Collection extends \Magestore\ReportSuccess\Model\ResourceModel\Product\Collection
{
    /**
     * @param $catalogPriceScope
     */
    public function calculateWebsiteScopeConfig($catalogPriceScope){
        // Run by cron is Global scope
        $reportManagement = \Magento\Framework\App\ObjectManager::getInstance()->get('Magestore\ReportSuccess\Api\ReportManagementInterface');
        if($reportManagement->isMSIEnable()){
            $qtyOnHandFieldName = self::QTY_ON_HAND;
            $warehouseIdFieldName = self::SOURCE_CODE;
        }else{
            $qtyOnHandFieldName = 'total_qty';
            $warehouseIdFieldName = 'warehouse_id';
        }

        $qtyOnHandSql = 'IFNULL(SUM(warehouse_product.'.$qtyOnHandFieldName.'),0)';
        $this->mappingField['warehouse'] = 'GROUP_CONCAT(warehouse_product.'.$warehouseIdFieldName. ')';
        $this->mappingWithoutMacField['warehouse'] = 'GROUP_CONCAT(warehouse_product.'.$warehouseIdFieldName. ')';

        $priceSql = 'IFNULL(`at_price`.`value`,0)';
        $macSql = 'IFNULL(`at_mac`.`value`,IF(`at_cost`.`value` is not null,at_cost.value,null))';
        $macWithoutPurchaseSql = '`at_cost`.`value`';
        $this->mappingField['qty_on_hand'] = $this->mappingFilterField['qty_on_hand'] = $qtyOnHandSql;
        $this->mappingField['potential_profit'] = $this->mappingFilterField['potential_profit'] = $priceSql. '*' .$qtyOnHandSql. '-' . $macSql. '*'. $qtyOnHandSql;
        $this->mappingField['stock_value'] = $this->mappingFilterField['stock_value'] = $macSql. '*'. $qtyOnHandSql;
        $this->mappingField['potential_revenue'] = $this->mappingFilterField['potential_revenue'] = $priceSql. '*'. $qtyOnHandSql;
        $this->mappingField['potential_margin'] = $this->mappingFilterField['potential_margin'] = '100*(1 - '.$macSql.'/'.$priceSql.')';
        $this->mappingField['price'] = $priceSql;
        $this->mappingField['mac'] = $macSql;

        $this->mappingWithoutMacField['qty_on_hand'] = $this->mappingFilterWithoutMacField['qty_on_hand'] = $qtyOnHandSql;
        $this->mappingWithoutMacField['potential_profit'] = $this->mappingFilterWithoutMacField['potential_profit'] = $priceSql. '*' .$qtyOnHandSql. '-' . $macWithoutPurchaseSql. '*'. $qtyOnHandSql;
        $this->mappingWithoutMacField['stock_value'] = $this->mappingFilterWithoutMacField['stock_value'] = $macWithoutPurchaseSql. '*'. $qtyOnHandSql;
        $this->mappingWithoutMacField['potential_revenue'] = $this->mappingFilterWithoutMacField['potential_revenue'] = $priceSql. '*'. $qtyOnHandSql;
        $this->mappingWithoutMacField['potential_margin'] = $this->mappingFilterWithoutMacField['potential_margin'] = '100*(1 - '.$macWithoutPurchaseSql.'/'.$priceSql.')';
        $this->mappingWithoutMacField['price'] = $priceSql;
        $this->mappingWithoutMacField['mac'] = $macWithoutPurchaseSql;
    }
}