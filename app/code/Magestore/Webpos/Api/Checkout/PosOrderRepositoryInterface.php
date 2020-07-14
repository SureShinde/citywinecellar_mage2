<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magestore\Webpos\Api\Checkout;

/**
 * Interface \Magestore\Webpos\Api\Checkout\PosOrderRepositoryInterface
 */
interface PosOrderRepositoryInterface
{
    /**
     * Process convert order
     *
     * @param string $incrementId
     * @return \Magestore\Webpos\Api\Data\Checkout\OrderInterface|bool
     * @throws \Exception
     */
    public function processConvertOrder($incrementId);
}
