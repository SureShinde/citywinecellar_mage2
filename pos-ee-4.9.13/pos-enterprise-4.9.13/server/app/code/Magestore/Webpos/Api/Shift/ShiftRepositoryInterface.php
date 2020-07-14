<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created by PhpStorm.
 * User: steve
 * Date: 06/06/2016
 * Time: 13:19
 */

namespace Magestore\Webpos\Api\Shift;

use Magestore\Webpos\Api\Data\Shift\ShiftInterface;


interface ShiftRepositoryInterface
{
    /**
     * @param int $shiftId
     * @return \Magestore\Webpos\Api\Data\Shift\ShiftInterface
     */
    public function get($shiftId);

    /**
     * get a list of Shift for a specific staff_id.
     * Because in the frontend we just need to show all shift for "this week"
     * so we will return this week shift only.
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria The search criteria.
     * @return \Magestore\Webpos\Api\Data\Shift\ShiftSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria);


    /**
     * @param  \Magestore\Webpos\Api\Data\Shift\ShiftInterface $shift
     * @return \Magestore\Webpos\Api\Data\Shift\ShiftInterface
     */
    public function save(ShiftInterface $shift);

    /**
     * Get current shift by pos id
     *
     * @param int $posId
     * @return \Magestore\Webpos\Api\Data\Shift\ShiftInterface|null
     */
    public function getCurrentShiftByPosId($posId);
}