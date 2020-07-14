<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosSampleData\Api;

interface PosRepositoryInterface
{
    /**
     * Save Role.
     *
     * @param \Magestore\PosSampleData\Api\Data\PosInterface $pos
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function forceSignOut(\Magestore\PosSampleData\Api\Data\PosInterface $pos);

}
