<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Country;

/**
 * Class CountryRepository
 * @package Magestore\Webpos\Model\Country
 */
class CountryRepository {

    /**
     * @var \Magento\Directory\Model\AllowedCountries
     */
    protected $allowedCountries;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Magestore\Webpos\Api\Data\Country\CountryInterfaceFactory
     */
    protected $countryInterfaceFactory;

    /**
     * @var
     */
    protected $countryInformationAcquirer;

    /**
     * @var \Magento\Directory\Api\CountryInformationAcquirerInterfaceFactory
     */
    protected $countryInformationAcquirerInterfaceFactory;

    /**
     * @var \Magestore\Webpos\Api\Data\Country\RegionInterfaceFactory
     */
    protected $regionInterfaceFactory;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * CountryRepository constructor.
     * @param \Magento\Directory\Model\AllowedCountries $allowedCountries
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magestore\Webpos\Api\Data\Country\CountryInterfaceFactory $countryInterfaceFactory
     * @param \Magestore\Webpos\Api\Data\Country\RegionInterfaceFactory $regionInterfaceFactory
     * @param \Magento\Directory\Api\CountryInformationAcquirerInterfaceFactory $countryInformationAcquirerInterfaceFactory
     * @param \Magento\Directory\Helper\Data $directoryHelper
     */
    public function __construct(
        \Magento\Directory\Model\AllowedCountries $allowedCountries,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magestore\Webpos\Api\Data\Country\CountryInterfaceFactory $countryInterfaceFactory,
        \Magestore\Webpos\Api\Data\Country\RegionInterfaceFactory $regionInterfaceFactory,
        \Magento\Directory\Api\CountryInformationAcquirerInterfaceFactory $countryInformationAcquirerInterfaceFactory,
        \Magento\Directory\Helper\Data $directoryHelper
    ){
        $this->allowedCountries = $allowedCountries;
        $this->countryFactory = $countryFactory;
        $this->countryInterfaceFactory = $countryInterfaceFactory;
        $this->regionInterfaceFactory = $regionInterfaceFactory;
        $this->countryInformationAcquirerInterfaceFactory = $countryInformationAcquirerInterfaceFactory;
        $this->directoryHelper = $directoryHelper;
    }

    /**
     * get list country
     *
     * @return \Magestore\Webpos\Api\Data\Country\CountryInterface[]
     */
    public function getList(){
        $countryArray = [];

        $allowedCountries = $this->allowedCountries->getAllowedCountries();
        $requiredStateCountry = $this->directoryHelper->getCountriesWithStatesRequired();

        foreach ($allowedCountries as $code) {

            $countryModel = $this->countryFactory->create()->loadByCode($code);
            $countryInstance = $this->countryInterfaceFactory->create();
            try {
                $info = $this->countryInformationAcquirerInterfaceFactory->create()->getCountryInfo($code);
                $name = $info->getFullNameLocale();
            } catch (\Exception $e) {
                $name = '';
            }
            $regionCollectionArray = [];
            $regionCollection = $countryModel->getRegions();
            if ($regionCollection->getSize()) {
                foreach($regionCollection as $region){
                    $regionModel = $this->regionInterfaceFactory->create();
                    $regionModel->setId($region->getId());
                    $regionModel->setName($region->getName());
                    $regionModel->setCode($region->getCode());
                    $regionCollectionArray[] = $regionModel;
                }
                $countryInstance->setRegions($regionCollectionArray);
            }
            if (in_array($code, $requiredStateCountry)) {
                $countryInstance->setRegionRequire(1);
            } else {
                $countryInstance->setRegionRequire(0);
            }

            $countryInstance->setId($countryModel->getId());
            $countryInstance->setName($name);
            $countryArray[] = $countryInstance;
        }

        return $countryArray;
    }
}