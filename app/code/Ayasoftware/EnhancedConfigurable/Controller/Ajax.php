<?php
/**
 * @category    Ayasoftware
 * @package     \Ayasoftware\EnhancedConfigurable
 * @author      EL HASSAN MATAR <support@ayasoftware.com>
 */
namespace Ayasoftware\EnhancedConfigurable\Controller;

class Ajax extends \Magento\Catalog\Controller\Product
{
    protected function _initProduct() {
        $productId = (int) $this->getRequest()->getParam('id');
        $parentId = (int) $this->getRequest()->getParam('pid');
        if ($productId) {
            $product = $this->_objectManager->create('Magento\Catalog\Model\Product')
                ->setStoreId($this->getStoreId())
                ->load($productId);
            if (!$product->getId()) {
                return false;
            }
            $this->_objectManager->get('Magento\Framework\Registry')->register('current_product', $product);
            $this->_objectManager->get('Magento\Framework\Registry')->register('product', $product);
            return $product;
        }

        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')
            ->setStoreId($this->getStoreId())
            ->load($parentId);

        if (!$product->getId()) {
            return false;
        }

        $this->_objectManager->get('Magento\Framework\Registry')->register('current_product', $product);
        $this->_objectManager->get('Magento\Framework\Registry')->register('product', $product);
        return $product;
    }
    public function execute(){
        
    }
    public function getStoreId()
    {
        return $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getStoreId();
    }
}
