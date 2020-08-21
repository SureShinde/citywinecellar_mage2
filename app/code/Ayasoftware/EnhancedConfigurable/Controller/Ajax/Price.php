<?php
/**
 * @category    Ayasoftware
 * @package     \Ayasoftware\EnhancedConfigurable
 * @copyright   2018 Ayasoftware (http://www.ayasoftware.com)
 * @author      EL HASSAN MATAR <support@ayasoftware.com>
 */
namespace Ayasoftware\EnhancedConfigurable\Controller\Ajax;

class Price extends \Ayasoftware\EnhancedConfigurable\Controller\Ajax
{
    public function execute()
    {
        $product = $this->_initProduct();
        if (!empty($product)) {
            $block =  $this->_view->loadLayout()->getLayout()->getBlock('product.product.price.info')->toHtml();
            $this->getResponse()->setBody($block);
        }
    }
}