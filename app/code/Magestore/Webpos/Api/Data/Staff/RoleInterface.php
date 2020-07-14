<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Staff;

/**
 * Interface RoleInterface
 * @package Magestore\Webpos\Api\Data\Staff
 */
interface RoleInterface
{
    /**
     *
     */
    const ROLE_ID = "role_id";
    /**
     *
     */
    const NAME = 'name';
    /**
     *
     */
    const DESCRIPTION = 'description';
    /**
     *
     */
    const MAXIMUM_DISCOUNT_PERCENT = 'maximum_discount_percent';

    /**
     * Get role id
     *
     * @api
     * @return int
     */
    public function getRoleId();

    /**
     * Set role id
     *
     * @api
     * @param int $roleId
     * @return RoleInterface
     */
    public function setRoleId($roleId);

    /**
     * Get name
     *
     * @api
     * @return int
     */
    public function getName();

    /**
     * Set name
     *
     * @api
     * @param int $name
     * @return RoleInterface
     */
    public function setName($name);

    /**
     * Get description
     *
     * @api
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     *
     * @api
     * @param string $description
     * @return RoleInterface
     */
    public function setDescription($description);

    /**
     * Get maximum discount percent
     *
     * @api
     * @return string|null
     */
    public function getMaximumDiscountPercent();

    /**
     * Set maximum discount percent
     *
     * @api
     * @param string $discount
     * @return RoleInterface
     */
    public function setMaximumDiscountPercent($discount);

}
