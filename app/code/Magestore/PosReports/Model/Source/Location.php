<?php
/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Magestore\PosReports\Model\Source;

/**
 * Class Location
 *
 * Used to create Location
 */
class Location extends \Magestore\Webpos\Model\Source\Adminhtml\Location
{

    const TOTAL = 0;
    const ALL_LOCATION_ID = 0;

    /**
     * @var bool
     */
    protected $showAllOption;

    /**
     * @var bool
     */
    protected $showTotalOption;

    /**
     * Location constructor.
     *
     * @param \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository
     * @param bool $showAllOption
     * @param bool $showTotalOption
     */
    public function __construct(
        \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository,
        $showAllOption = false,
        $showTotalOption = false
    ) {
        parent::__construct($locationRepository);
        $this->showAllOption = $showAllOption;
        $this->showTotalOption = $showTotalOption;
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        if ($this->showAllOption) {
            $options[] = ['label' => __('All Locations'), 'value' => self::ALL_LOCATION_ID];
        }
        $allLocationArray = parent::toOptionArray();
        $options = array_merge($options, $allLocationArray);
        if (!$this->showAllOption && $this->showTotalOption) {
            $options[] = ['label' => __('Total'), 'value' => self::TOTAL];
        }
        return $options;
    }

    /**
     * Get option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        if ($this->showAllOption) {
            $options[self::ALL_LOCATION_ID] = __('All Locations');
        }
        $allLocationArray = parent::getOptionArray();
        $options = array_merge($options, $allLocationArray);
        if (!$this->showAllOption && $this->showTotalOption) {
            $options[self::TOTAL] = __('Total');
        }
        return $options;
    }
}
