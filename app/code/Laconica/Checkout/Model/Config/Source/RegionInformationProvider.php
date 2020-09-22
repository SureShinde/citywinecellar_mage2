<?php

namespace Laconica\Checkout\Model\Config\Source;

class RegionInformationProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformationAcquirer
     */
    private $countryInformationAcquirer;

    public function __construct(
        \Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformationAcquirer
    )
    {
        $this->countryInformationAcquirer = $countryInformationAcquirer;
    }

    public function toOptionArray()
    {
        $countries = $this->countryInformationAcquirer->getCountriesInfo();
        $regions = [];
        foreach ($countries as $country) {
            if ($country->getId() !== "US") {
                continue;
            }

            $availableRegions = $country->getAvailableRegions();
            foreach ($availableRegions as $region) {
                $regions[] = [
                    'label' => $region->getName(),
                    'value' => $region->getId()
                ];
            }

        }
        return $regions;
    }
}