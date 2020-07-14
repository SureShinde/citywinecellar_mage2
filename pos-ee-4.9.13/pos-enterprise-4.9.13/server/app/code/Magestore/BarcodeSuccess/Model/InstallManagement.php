<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Model;

use Magestore\BarcodeSuccess\Api\InstallManagementInterface;

class InstallManagement implements InstallManagementInterface
{

    /**
     *
     * @var \Magestore\BarcodeSuccess\Model\ResourceModel\InstallManagement
     */
    protected $_resource;

    /**
     * InstallManagement constructor.
     * @param ResourceModel\InstallManagement $installManagementResource
     */
    public function __construct(
        \Magestore\BarcodeSuccess\Model\ResourceModel\InstallManagement $installManagementResource
    )
    {
        $this->_resource = $installManagementResource;
    }

    /**
     * @inheritdoc
     */
    public function createBarcodeTemplate()
    {
        $this->getResource()->createBarcodeTemplate();
        return $this;
    }

    /**
     *
     * @return \Magestore\BarcodeSuccess\Model\ResourceModel\InstallManagement
     */
    public function getResource()
    {
        return $this->_resource;
    }
}