<?php

namespace Magestore\ReportSuccess\Api;

/**
 * Class ReportManagementInterface
 * @package Magestore\ReportSuccess\Api
 */
interface ReportManagementInterface
{
    /**
     * @return boolean
     */
    public function isMSIEnable();

    /**
     * @return boolean
     */
    public function isInventorySuccessEnable();

    /**
     * @return mixed
     */
    public function isFulFilSuccessEnable();
}