<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Model;

/**
 * Class Product
 *
 * Use for BarcodeSuccess
 */
class Product extends \Magento\Catalog\Model\Product
{
    /**
     * Migrate Barcode
     *
     * @param string $attributeCode
     * @param int|string|null $historyId
     */
    public function migrateBarcode($attributeCode, $historyId)
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $barcodeModel = $objectManager->create(\Magestore\BarcodeSuccess\Model\Barcode::class);
        if ($attributeCode) {
            $barcodeAttribute = $this->getResource()->getAttribute($attributeCode);
            if ($barcodeAttribute) {
                $attributeValue = $barcodeAttribute->getFrontend()->getValue($this);
                if ($attributeValue) {
                    $data = [
                        'barcode' => $attributeValue,
                        'product_id' => $this->getId(),
                        'qty' => 1,
                        'product_sku' => $this->getSku(),
                        'supplier_code' => '',
                        'history_id' => $historyId
                    ];
                    $barcodeModel->setData($data);
                    $barcodeModel->save();
                }
            }
        }
    }
}
