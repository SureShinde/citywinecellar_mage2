<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Config;

interface ConfigRepositoryInterface {

    /**
     * get list config
     *
     * @return \Magestore\Webpos\Api\Data\Config\ConfigInterface
     */
    public function getAllConfig();
}