<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosSampleData\Api;

interface ConfigRepositoryInterface
{
    /**
     * Save Role.
     *
     * @param \Magestore\PosSampleData\Api\Data\ConfigInterface $config
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function changeConfig(\Magestore\PosSampleData\Api\Data\ConfigInterface $config);

}
