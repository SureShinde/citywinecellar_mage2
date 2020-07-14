<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Block\Barcode\Container;

class Scan extends \Magestore\BarcodeSuccess\Block\Barcode\Container
{

    public function getWidgetInitData(){
        $data = [];
        $widget = $this->getData('widget');
        if(isset($widget) && count($widget) > 0){
            foreach ($widget as $widgetCode => $widgetData){
                $code = (isset($widgetData['code']))?$widgetData['code']:$widgetCode;
                $data[$code] = (isset($widgetData['config']))?$widgetData['config']:[];
                if(isset($widgetData['config']) && isset($widgetData['config']['urls'])){
                    $urls = $widgetData['config']['urls'];
                    if(count($urls) > 0){
                        foreach ($urls as $key => $path){
                            $data[$code]['urls'][$key] = $this->getOsUrl($path);
                        }
                    }
                }
            }
        }
        return \Zend_Json::encode($data);
    }

    public function getPanels(){
        return $this->getData('os_panels');
    }

    public function hasPanels(){
        $panels = $this->getPanels();
        return (isset($panels) && count($panels) > 0)?true:false;
    }

    public function getOsUrl($path){
        return $this->backendHelper->getUrl($path);
    }
}
