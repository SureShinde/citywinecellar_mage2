<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Model\Indexer\Product\System\Config;

/**
 * Backend Model of config BarcodeAttribute
 */
class BarcodeAttribute extends \Magento\Framework\App\Config\Value
{
    /**
     * Set after commit callback
     *
     * @return $this
     */
    public function afterSave()
    {
        $this->_getResource()->addCommitCallback([$this, 'processValue']);
        return parent::afterSave();
    }

    /**
     * Require reindex Webpos Product Search indexer when changing barcode attribute
     *
     * @return void
     */
    public function processValue()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\Module\Manager $moduleManager */
        $moduleManager = $objectManager->create(\Magento\Framework\Module\Manager::class);
        if ($moduleManager->isEnabled('Magestore_Webpos')) {
            /** @var \Magestore\Webpos\Helper\Data $posHelper */
            $posHelper = $objectManager->create(\Magestore\Webpos\Helper\Data::class);

            if ($this->isValueChanged() && $posHelper->isEnableElasticSearch()) {
                /** @var \Magestore\Webpos\Model\Indexer\Product\Processor $posIndexerProcessor */
                $posIndexerProcessor = $objectManager->create(
                    \Magestore\Webpos\Model\Indexer\Product\Processor::class
                );
                $posIndexerProcessor->markIndexerAsInvalid();
            }
        }

        // Generate barcode when changing barcode attribute
        if ($this->isValueChanged()) {
            /** @var \Magestore\BarcodeSuccess\Helper\Attribute $barcodeAttribute */
            $barcodeAttribute = $objectManager->create(\Magestore\BarcodeSuccess\Helper\Attribute::class);
            $barcodeAttribute->importToBarcode($this->getValue());
        }
    }
}
