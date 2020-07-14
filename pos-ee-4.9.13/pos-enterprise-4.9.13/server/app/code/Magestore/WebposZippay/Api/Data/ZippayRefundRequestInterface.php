<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposZippay\Api\Data;

/**
 * Interface ZippayServiceInterface
 * @package Magestore\WebposZippay\Api\Data
 */
interface ZippayRefundRequestInterface
{
    const CREDITMEMO = 'creditmemo';

    /**
     * @return \Magestore\Webpos\Api\Data\Sales\Order\CreditmemoInterface
     */
    public function getCreditmemo();

    /**
     * @param \Magestore\Webpos\Api\Data\Sales\Order\CreditmemoInterface
     * @return ZippayRefundRequestInterface
     */
    public function setCreditmemo($creditmemo);

}
