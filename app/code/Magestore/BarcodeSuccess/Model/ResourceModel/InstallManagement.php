<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Model\ResourceModel;

use Magestore\BarcodeSuccess\Api\Db\QueryProcessorInterface;

class InstallManagement extends AbstractResource
{
    protected function _construct()
    {
        /* do nothing */
    }

    /**
     * Create default barcode template
     *
     * @return \Magestore\BarcodeSuccess\Model\ResourceModel\InstallManagement
     */
    public function createBarcodeTemplate()
    {
        /* start query process */
        $this->_queryProcessor->start();

        $defaultData = $this->barcodeTemplates->getDefaultData();

        /* add query to Processor */
        $this->_queryProcessor->addQuery([
            'type' => QueryProcessorInterface::QUERY_TYPE_INSERT,
            'values' => $defaultData,
            'table' => $this->getTable('os_barcode_template')
        ]);

        /* process queries in Processor */
        $this->_queryProcessor->process();

        return $this;
    }

}