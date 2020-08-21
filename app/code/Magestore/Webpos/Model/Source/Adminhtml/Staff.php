<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Source\Adminhtml;

/**
 * Source option Staff
 */
class Staff implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface
     */
    public $staffRepository;

    /**
     * Pos constructor.
     *
     * @param \Magestore\Webpos\Api\Pos\PosRepositoryInterface $staffRepository
     */
    public function __construct(
        \Magestore\Appadmin\Api\Staff\StaffRepositoryInterface $staffRepository
    ) {
        $this->staffRepository = $staffRepository;
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $allStaff = $this->staffRepository->getAllStaff();
        $allStaffArray = [];
        foreach ($allStaff as $staff) {
            $allStaffArray[] = ['label' => $staff->getUsername(), 'value' => $staff->getId()];
        }
        return $allStaffArray;
    }

    /**
     * Get options array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $allStaff = $this->staffRepository->getAllStaff();
        $allStaffArray = [];
        foreach ($allStaff as $staff) {
            $allStaffArray[$staff->getId()] = $staff->getUsername();
        }
        return $allStaffArray;
    }

    /**
     * OptionArrayToShow
     *
     * @return array
     */
    public function optionArrayToShow()
    {
        $options = [];
        $options[] = ['value' => null, 'label' => ' '];
        $optionsArr = $this->toOptionArray();
        $options = array_merge($options, $optionsArr);
        return $options;
    }
}
