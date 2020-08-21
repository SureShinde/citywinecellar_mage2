<?php

/**
 * Copyright   2018 Ayasoftware (http://www.ayasoftware.com).
 * See COPYING.txt for license details.
 * author      EL HASSAN MATAR <support@ayasoftware.com>
 */

namespace Ayasoftware\EnhancedConfigurable\Helper;

use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;
    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
    Context $context, \Magento\Framework\ObjectManagerInterface $objectManager,
            \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->objectManager = $objectManager;
        $this->_storeManager = $storeManager;
    }

    /**
     * Get Cheapest Child Price
     * @param type $product
     * @return $prices or false (if not a configurable product)
     */
    public function getCheapestChildPrice($product) {
        if($product->getTypeId() != 'configurable') {
            return false;
        }

        $productIds = array();
        $conf = $this->objectManager->get('Magento\ConfigurableProduct\Model\Product\Type\Configurable');
        $childProducts = $conf->getUsedProductCollection($product)
                ->addAttributeToSelect(
                ['msrp', 'price', 'special_price', 'status', 'special_from_date', 'special_to_date']
        );
        foreach($childProducts as $childProduct) {
            if( ! $childProduct->isSalable()) {
                continue;
            }
            $finalPrice = $childProduct->getFinalPrice();
            if($childProduct->getTierPrice()) {
                $tprices = array();
                foreach($tierprices = $childProduct->getTierPrice() as $tierprice) {
                    $tprices[] = $tierprice['price'];
                }
            }
            if( ! empty($tprices)) {
                $finalPrice = min($tprices);
            }

            $this->_eventManager->dispatch('catalog_product_get_final_price', ['product' => $childProduct, 'qty' => 1]);
            if($childProduct->isSalable()) {
                $productIds[$childProduct->getId()] = ["finalPrice" => $childProduct->getFinalPrice(), "price" => $childProduct->getPrice()];
            }
        }
        if(empty($productIds)) {
            return false;
        }
        $productCheapestId = array_search(min($productIds), $productIds);
        $productExpensiveId = array_search(max($productIds), $productIds);
        $prices = array();
        $prices["Min"] = ["finalPrice" => $productIds[$productCheapestId]['finalPrice'], "price" => $productIds[$productCheapestId]['price']];
        $prices["Max"] = ["finalPrice" => $productIds[$productExpensiveId]['finalPrice'], "price" => $productIds[$productExpensiveId]['price']];

        return $prices;
    }

    public function applyRulesToProduct($product) {
        $rule = $this->objectManager->get('Magento\CatalogRule\Model\Rule');
        return $rule->calcProductPriceRule($product, $product->getPrice());
    }

    public function canApplyTierPrice($product, $qty) {
        $tierPrice = $product->getTierPrice($qty);
        if(empty($tierPrice)) {
            return false;
        }
        $price = $product->getPrice();
        if($tierPrice != $price) {
            return true;
        } else {
            return false;
        }
    }

    public function applyOptionsPrice($product, $finalPrice) {
        if($optionIds = $product->getCustomOption('option_ids')) {
            $basePrice = $finalPrice;
            foreach(explode(',', $optionIds->getValue()) as $optionId) {
                if($option = $product->getOptionById($optionId)) {
                    $confItemOption = $product->getCustomOption('option_' . $option->getId());
                    $group = $option->groupFactory($option->getType())
                            ->setOption($option)
                            ->setConfigurationItemOption($confItemOption);
                    $finalPrice += $group->getOptionPrice($confItemOption->getValue(), $basePrice);
                }
            }
        }
        return $finalPrice;
    }

    public function getProductDetails($product) {
        $reloadedProduct = $this->objectManager->create('Magento\Catalog\Model\Product')->load($product->getId());
        $data =array(); 
        $data["description"] = $reloadedProduct->getData('description');
        $data["short_description"] = $reloadedProduct->getData('short_description');
        return $data; 
    }
    
    // get simple products url key
    public function getRealUrlKey($product_id) {
        
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
       $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
       $connection = $resource->getConnection();
       $coreUrlRewritesTable = $resource->getTableName('url_rewrite'); //gives table name with prefix
       $storeId =  $this->getStoreId(); 
        $configurableSelect = $connection->select()
                ->from(array('c' => $coreUrlRewritesTable), 'c.request_path')
                ->where("c.entity_id = ?", $product_id)
                ->where("c.store_id = ?", $storeId);
        $prodIds = $connection->fetchAll($configurableSelect);

        if( ! empty($prodIds)) {
            return $prodIds[0];
        } else {
            return false;
        }
    }
    
    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
    
    

}
