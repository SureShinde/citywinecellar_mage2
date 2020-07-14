<?php

namespace Magestore\Webpos\Test\Api\PlaceOrders\RequestData;

/**
 * Class ItemsData
 * @package Magestore\Webpos\Test\Api\PlaceOrders\RequestData
 */
class ItemsData
{
    public function getSchemaJson($currentTimeStamp, $sku, $name , $productId, $qtyOrdered )
    {
        $itemData =  '{      
				"additional_data" : "",
				"applied_rule_ids" : 5,
				"base_discount_amount" : 20,
				"base_discount_tax_compensation_amount" : 0,
				"base_gift_voucher_discount" : 0,
				"base_original_price" : 35,
				"base_price" : 35,
				"base_price_incl_tax" : "37.89",
				"base_row_total" : 35,
				"base_row_total_incl_tax" : 37.89,
				"base_tax_amount" : 1.24,
				"base_tax_before_discount" : 0,
				"description" :"",
				"discount_amount" : 20,
				"discount_percent" : 0,
				"discount_tax_compensation_amount" : 0,
				"free_shipping" : 0,
				"gift_voucher_discount" : 0,
				"giftcodes_applied" : null,
				"is_qty_decimal" : 0,
				"is_virtual" : 0,		
				"magestore_base_discount" : 0,
				"magestore_discount" : 0,				
				"original_price" : 35,
				"parent_item_id" : null,
				"pos_base_original_price_excl_tax" : 35,
				"pos_base_original_price_incl_tax" : 37.89,
				"pos_original_price_excl_tax" : 35,
				"pos_original_price_incl_tax" : 37.89,
				"price" : 35,
				"price_incl_tax" : 37.89,
				"product_options" : "",
				"product_type" : "simple",
				"row_total" : 35,
				"row_total_incl_tax" : 37.89,			
				"tax_amount" : 1.24,
				"tax_before_discount" : 0,
				"tax_percent" : 8.25,
				"weight" : null,
				"item_id" : %s ,
				"order_id" : %s ,
				"quote_item_id" : %s ,
				"sku" : "%s" ,
				"name" : "%s" ,
				"product_id" : %s,
				"qty_ordered" : %s
			}
        ';

        return sprintf($itemData,
            $currentTimeStamp,
            $currentTimeStamp,
            $currentTimeStamp,
            $sku,
            $name,
            $productId,
            $qtyOrdered
        );
    }
}