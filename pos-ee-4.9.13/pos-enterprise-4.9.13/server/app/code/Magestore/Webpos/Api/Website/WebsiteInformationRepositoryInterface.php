<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Website;

interface WebsiteInformationRepositoryInterface
{
    /**
     * get all config
     *
     * @return \Magestore\Webpos\Api\Data\Website\WebsiteInformationInterface
     */
    public function getInformation();
}
