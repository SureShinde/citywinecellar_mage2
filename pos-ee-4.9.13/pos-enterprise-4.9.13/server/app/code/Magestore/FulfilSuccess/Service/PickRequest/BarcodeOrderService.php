<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Service\PickRequest;

use Magestore\FulfilSuccess\Service\Barcode\BarcodeServiceInterface;

class BarcodeOrderService
{
    /**
     * Pick Code Prefix
     */
    const PICK_CODE_PREFIX = 'PICK';
    
    /**
     * Pack Code Prefix
     */
    const PACK_CODE_PREFIX = 'PACK';
    
    /**
     * Define barcode type
     */
    const BARCODE_TYPE_ORDER = 1;
    const BARCODE_TYPE_ITEM = 2;
    const BARCODE_TYPE_PICK = 'pick';
    const BARCODE_TYPE_PACK = 'pack';

    /**
     * @var BarcodeServiceInterface 
     */
    var $barcodeService;
    
    
    public function __construct(BarcodeServiceInterface $barcodeService)
    {
        $this->barcodeService = $barcodeService;
    }
    /**
     * Generate specify barcode
     * 
     * @param string $id
     * @return string
     */
    public function generatePickCode($id)
    {
        return self::PICK_CODE_PREFIX . $id;
    }
    /**
     * Generate specify barcode
     *
     * @param string $id
     * @return string
     */
    public function generatePackCode($id)
    {
        return self::PACK_CODE_PREFIX . $id;
    }
    
    /**
     * 
     * @param string $barcodeString
     * @return int
     */
    public function getBarcodeType($barcodeString)
    {
        $prexfix = substr($barcodeString, 0, 5);
        if($prexfix == self::PICK_CODE_PREFIX) {
            return self::BARCODE_TYPE_ORDER;
        } else {
            return self::BARCODE_TYPE_ITEM;
        }
    }
    
    /**
     * get barcode source
     * 
     * @param string $data
     * @return string
     */
    public function getBarcodeSource($data, $type = "pick")
    {
        switch ($type){
            case self::BARCODE_TYPE_PICK:
                $formatNumber = $this->generatePickCode($data);
                break;
            case self::BARCODE_TYPE_PACK:
                $formatNumber = $this->generatePackCode($data);
                break;
        }
        $barcode = $this->barcodeService->getBarcodeSource($formatNumber);
        return base64_encode($barcode);        
    }
    
    /**
     * get Sales Increment Id from barcode string
     * 
     * @param type $barcodeString
     * @return string
     */
    public function getOrderIdFromBarcode($barcodeString)
    {
        if($this->getBarcodeType($barcodeString) == self::BARCODE_TYPE_ORDER) {
            return str_replace(self::PICK_CODE_PREFIX, '', $barcodeString);
        }
        return null;
    }
}