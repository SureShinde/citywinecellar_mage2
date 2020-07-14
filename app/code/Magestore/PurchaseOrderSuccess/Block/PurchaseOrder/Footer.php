<?php

namespace Magestore\PurchaseOrderSuccess\Block\PurchaseOrder;

class Footer extends \Magestore\PurchaseOrderSuccess\Block\PurchaseOrder\AbstractBlock {

    protected $width = '100%';

    public function getWidth(){
        return $this->width;
    }

    public function setWidth($width){
        $this->width = $width;
        return $this;
    }
}