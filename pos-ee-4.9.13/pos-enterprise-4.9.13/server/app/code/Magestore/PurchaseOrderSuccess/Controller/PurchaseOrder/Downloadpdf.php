<?php

namespace Magestore\PurchaseOrderSuccess\Controller\PurchaseOrder;

use Magento\Framework\App\Filesystem\DirectoryList;

class Downloadpdf extends \Magestore\PurchaseOrderSuccess\Controller\AbstractController {
    public function execute() {
        $purchaseKey = $this->getRequest()->getParam('key');
        $urlBuilder = $this->_objectManager->get('Magento\Framework\Url');
        $url = $urlBuilder->getUrl('purchaseordersuccess/purchaseOrder/dataprint', ['key' => $purchaseKey]);
        $return = "
                <script>
                    window.open('".$url."','PrintWindow', 'width=500,height=500,top=200,left=200').print();
                    
                    setTimeout(function(){ 
                        close(); 
                        }, 100
                    );
                </script>
            ";
        return $this->getResponse()->setBody($return);
    }
}