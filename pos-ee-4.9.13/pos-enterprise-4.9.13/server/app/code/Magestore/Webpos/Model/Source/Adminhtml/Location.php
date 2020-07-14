<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Source\Adminhtml;
class Location implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magestore\Webpos\Api\Location\LocationRepositoryInterface
     */
    public $locationRepository;

    /**
     * Location constructor.
     * @param \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository
     */
    public function __construct(
        \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository
    ){
        $this->locationRepository = $locationRepository;
    }


    /**
     * @return array
     */
    public function toOptionArray()
    {
        $allLocation = $this->locationRepository->getAllLocation();
        $allLocationArray = [];
        foreach ($allLocation as $location) {
            $allLocationArray[] = ['label' => $location->getName(), 'value' => $location->getLocationId()];
        }
        return $allLocationArray;
    }

    /**
     * @return array
     */
    public function getOptionArray()
    {
        $allLocation = $this->locationRepository->getAllLocation();
        $allLocationArray = [];
        foreach ($allLocation as $location) {
            $allLocationArray[$location->getLocationId()] = $location->getName();
        }
        return $allLocationArray;
    }

}