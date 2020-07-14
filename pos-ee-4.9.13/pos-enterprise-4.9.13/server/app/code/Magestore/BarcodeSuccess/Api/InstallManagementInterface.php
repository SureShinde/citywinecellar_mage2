<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Api;


interface InstallManagementInterface
{

    /**
     * Create default barcode template
     *
     * @return \Magestore\BarcodeSuccess\Api\InstallManagementInterface
     */
    public function createBarcodeTemplate();
}