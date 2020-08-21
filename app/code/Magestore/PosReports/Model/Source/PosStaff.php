<?php
/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Model\Source;

/**
 * Class PosStaff
 *
 * Used to create Pos Staff
 */
class PosStaff extends \Magestore\Webpos\Model\Source\Adminhtml\Staff
{

    const ALL_STAFF_ID = 0;

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $allLocationArray = parent::toOptionArray();
        $allLocationArray[] = ['label' => __('Total'), 'value' => self::ALL_STAFF_ID];
        return $allLocationArray;
    }

    /**
     * Get option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $allLocationArray = parent::getOptionArray();
        $allLocationArray[self::ALL_STAFF_ID] = __('Total');
        return $allLocationArray;
    }
}
