<?php
/**
 * @category    Ayasoftware
 * @package     \Ayasoftware\EnhancedConfigurable
 * @copyright   2015 Ayasoftware (http://www.ayasoftware.com)
 * @author      EL HASSAN MATAR <support@ayasoftware.com>
 */

namespace Ayasoftware\EnhancedConfigurable\Controller\Ajax;

class Options extends \Ayasoftware\EnhancedConfigurable\Controller\Ajax {
    
    public function execute() {
        
        $preSelect = false; 
        $customerSession = $this->_objectManager->create('Magento\Customer\Model\Session');
        if( ! empty($customerSession->getData('ayasoftware_configurableproducts'))) {
            $options = $customerSession->getData('ayasoftware_configurableproducts');
            if(isset($options['child_id']) && isset($options['parent_id'])) {
                $preSelect = true;
                $parent_id = $options['parent_id'];
                $product = $this->getLoadProduct($options['child_id']);
                $product_sku = $product->getSku();
            }
        }
        if($preSelect){
            $configurableProduct = $this->getLoadProduct($parent_id);
            $allValues = array();
            $productAttributesOptions = $configurableProduct->getTypeInstance(true)->getConfigurableOptions($configurableProduct);
            foreach($productAttributesOptions as $productAttributeOption) {
                $options[$product->getId()] = array();
                $products_ids = array();
                $products = array();
                foreach($productAttributeOption as $optionValues) {
                       if($product_sku == $optionValues['sku']) {
                         $products[] = $optionValues['sku']; 
                         $allValues[$optionValues['value_index']] =  array("code" => $optionValues['attribute_code'], 'products' => $products);
                       }
                }
            }
            $defaultValues = array();
            foreach ($allValues as $key => $optionValue) {
                if(in_array($product_sku, $optionValue['products'])) {
                   $defaultValues[$optionValue['code']]= $key;
                }
            } 
            echo  $this->_objectManager->create('Magento\Framework\Json\EncoderInterface')->encode($defaultValues);
         } else {
             echo  $this->_objectManager->create('Magento\Framework\Json\EncoderInterface')->encode(array());
         }
    }
    
    public function getLoadProduct($id)
    {
        return $this->_objectManager->create('Magento\Catalog\Model\ProductFactory')->create()->load($id);
        
    }
    
    public function getStoreId() {
        return $this->_objectManager->create('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
    }

}
