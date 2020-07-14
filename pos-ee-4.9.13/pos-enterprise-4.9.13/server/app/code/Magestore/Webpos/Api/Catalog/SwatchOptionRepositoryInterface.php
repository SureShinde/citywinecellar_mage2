<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Catalog;

/**
 * @api
 */
interface SwatchOptionRepositoryInterface
{
    /**
     * @return \Magestore\Webpos\Api\Data\Catalog\Option\SwatchOptionSearchResultsInterface
     */
    public function getSwatchOptions();
}