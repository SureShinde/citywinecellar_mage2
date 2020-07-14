<?php

namespace Magestore\PurchaseOrderSuccess\Model\Export;

class ConvertToXml extends \Magento\Ui\Model\Export\ConvertToXml
{
    public function getFileName(){
        return $this->filter->getComponent()->getName();
    }
}