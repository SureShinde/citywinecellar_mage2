<?php

namespace Magestore\PurchaseOrderSuccess\Model\Export;

class ConvertToCsv extends \Magento\Ui\Model\Export\ConvertToCsv
{
    public function getFileName(){
        return $this->filter->getComponent()->getName();
    }
}