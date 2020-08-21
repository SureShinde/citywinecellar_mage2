<?php

namespace Ayasoftware\EnhancedConfigurable\Controller\Ajax;

class Additional extends \Ayasoftware\EnhancedConfigurable\Controller\Ajax
{
    public function execute()
    {
      $product = $this->_initProduct();
        if (!empty($product)) {
           $block =  $this->_view->loadLayout()->getLayout()->getBlock('product.attributes')->toHtml();
            $this->getResponse()->setBody($block);
        }
    }
}
