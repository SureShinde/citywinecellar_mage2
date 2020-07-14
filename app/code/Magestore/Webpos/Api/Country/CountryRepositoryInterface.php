<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Country;

interface CountryRepositoryInterface {

    /**
     * get list country
     *
     * @return \Magestore\Webpos\Api\Data\Country\CountryInterface[]
     */
    public function getList();
}