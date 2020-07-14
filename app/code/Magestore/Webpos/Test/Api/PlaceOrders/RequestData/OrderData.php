<?php


namespace Magestore\Webpos\Test\Api\PlaceOrders\RequestData;

use Magento\TestFramework\Helper\Bootstrap;
/**
 * Class ItemsData
 * @package Magestore\Webpos\Test\Api\PlaceOrders\RequestData
 */
class OrderData
{
    protected  $timeZone;
    /**
     * OrderData constructor.
     */
    public function __construct()
    {
        $this->timeZone = Bootstrap::getObjectManager()->get('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
    }

    /**
     * @return string
     */
    public function getAddress($currentTimeStamp){
        $address = '[ %s, %s ]';
        $billing = $this->billingData($currentTimeStamp);
        $shipping = $this->shippingData($currentTimeStamp);
        return sprintf($address,
            $billing,
            $shipping
        );
    }

    /**
     * @return string
     */
    public function billingData($currentTimeStamp){
        $billingData =  '{
            "parent_id": %s,
            "address_type":"billing",
            "city":"Calder",
            "company":"N/A",
            "country_id":"US",
            "customer_address_id":null,
            "customer_id":null,
            "email":"guest@example.com",
            "fax":"",
            "firstname":"Guest",
            "lastname":"POS",
            "middlename":null,       
            "postcode":"49628-7978",
            "prefix":"",
            "quote_address_id":null,
            "region":"Michigan",
            "region_id":33,
            "suffix":"",
            "telephone":"",
            "street": ["6146 Honey Bluff Parkway"]
        }';
        return sprintf($billingData, $currentTimeStamp);
    }

    public function shippingData($currentTimeStamp){
        $shippingData='{
            "parent_id": %s,
            "address_type":"shipping",
            "city":"Calder",
            "company":"N/A",
            "country_id":"US",
            "customer_address_id":null,
            "customer_id":null,
            "email":"guest@example.com",
            "fax":"",
            "firstname":"Guest",
            "lastname":"POS",
            "middlename":null,
            "postcode":"49628-7978",
            "prefix":"",
            "quote_address_id":null,
            "region":"Michigan",
            "region_id":33,
            "street":["6146 Honey Bluff Parkway"],
            "suffix":"",
            "telephone":""
        }';
        return sprintf($shippingData, $currentTimeStamp);
    }

    /**
     * @param $currentTimeStamp
     * @return string
     */
    public function getpayment($currentTimeStamp, $paymentMethod){
        $incrementId = "1-".$currentTimeStamp;
        $payment = '[{
            "amount_paid":16.24,
            "base_amount_paid":16.24,
            "increment_id": "%s",
            "method":"%s",
            "payment_date":"2018-12-20 07:20:45",
            "reference_number":"",
            "shift_increment_id":"",
            "title":"Cash"
        }]';
        return sprintf($payment, $incrementId,$paymentMethod);
    }

    /**
     * @param $currentTimeStamp
     * @param $paymentMethod
     * @param $items
     * @return string
     */
    public function getOrderData($currentTimeStamp,$paymentMethod,$items){
        $address = $this->getAddress($currentTimeStamp);
        $increment = "1-".$currentTimeStamp;
        $payment = $this->getpayment($currentTimeStamp,$paymentMethod);
        $orderData='
        {
            "entity_id": %s,       
            "quote_id": %s,
            "increment_id": "%s",
            "addresses": %s,       
            "payments": %s,
            "items": %s,         
            "applied_rule_ids":"5",
            "base_currency_code":"USD",
            "base_discount_amount":-20,
            "base_discount_tax_compensation_amount":0,
            "base_grand_total":16.24,
            "base_pos_change":0,
            "base_shipping_amount":0,
            "base_shipping_discount_amount":0,
            "base_shipping_incl_tax":0,
            "base_shipping_tax_amount":0,
            "base_subtotal":35,
            "base_subtotal_incl_tax":37.89,
            "base_tax_amount":1.24,
            "base_to_global_rate":1,
            "base_to_order_rate":1,
            "base_total_due":0,
            "base_total_paid":16.24,
            "coupon_code":"",
            "created_at":"2018-12-20 07:20:46",
            "customer_dob":"",
            "customer_email":"guest@example.com",
            "customer_firstname":"Guest",
            "customer_gender":null,
            "customer_group_id":0,
            "customer_id":null,
            "customer_is_guest":1,
            "customer_lastname":"POS",
            "customer_middlename":"",
            "customer_note":"",
            "customer_prefix":"",
            "customer_suffix":"",
            "customer_taxvat":"",
            "discount_amount":-20,
            "discount_description":"",
            "discount_tax_compensation_amount":0,
            "email_sent":0,         
            "global_currency_code":"USD",
            "grand_total":16.24,
            "is_virtual":0,
            "order_currency_code":"USD",
            "os_pos_custom_discount_amount":0,
            "os_pos_custom_discount_reason":"",
            "os_pos_custom_discount_type":"",
            "pos_change":0,
            "pos_delivery_date":null,
            "pos_fulfill_online":0,
            "pos_id":"1",
            "pos_location_id":1,
            "pos_staff_id":"1",
            "pos_staff_name":"duongdiep duongdiep",
            "quote_address_id":null,       
            "send_email":1,
            "shipping_amount":0,
            "shipping_description":"Pickup-at-store",
            "shipping_discount_amount":0,
            "shipping_discount_tax_compensation_amount":0,
            "shipping_incl_tax":0,
            "shipping_method":"webpos_shipping_storepickup",
            "shipping_tax_amount":0,
            "state":"new",
            "status":"pending",
            "store_currency_code":"USD",
            "store_name":"",
            "store_to_base_rate":1,
            "store_to_order_rate":1,
            "subtotal":35,
            "subtotal_incl_tax":37.89,
            "tax_amount":1.24,
            "total_due":0,
            "total_item_count":1,
            "total_paid":16.24,
            "total_qty_ordered":1,
            "updated_at":"2018-12-20 07:20:46",
            "weight":0
        }';

        return sprintf($orderData,
            $currentTimeStamp,
            $currentTimeStamp,
            $increment,
            $address,
            $payment,
            $items
        );
    }

    public function getSchemaJson($ship, $invoice, $currentTimeStamp, $paymentMethod, $items)
    {
         $order = $this->getOrderData($currentTimeStamp,$paymentMethod,$items);
         $requestData =  '{      
				 "create_invoice": %s,
                 "create_shipment": %s,
                 "order" : %s
		 }
         ';

        return sprintf($requestData,
            $ship,
            $invoice,
            $order
        );
    }
}