<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosSampleData\Api\Data;

/**
 * @api
 */
interface PosInterface
{
    const POS_NAME = 'pos_name';
    const USERNAME = 'username';

    /**
     * Get pos name
     *
     * @api
     * @return string
     */
    public function getPosName();

    /**
     * Set pos name
     *
     * @api
     * @param string $posName
     * @return \Magestore\PosSampleData\Api\Data\PosInterface
     */
    public function setPosName($posName);

    /**
     * Get username
     *
     * @api
     * @return string
     */
    public function getUsername();

    /**
     * Set username
     *
     * @api
     * @param string $username
     * @return \Magestore\PosSampleData\Api\Data\PosInterface
     */
    public function setUsername($username);

}
