<?php

/**
 * Location simple Data
 */
namespace Magestore\Webpos\Test\Constant;
use Magestore\Webpos\Api\Data\Location\LocationInterface;
use Magestore\Webpos\Test\Constant\Stock as Stock;

/**
 * Class Location
 * @package Magestore\Webpos\Test\Constant
 */
class Location
{
    const NAME = 'Location Test';
    const STREET = '6146 Honey Bluff Parkway';
    const CITY = 'Calder';
    const REGION = 'Michigan';
    const REGION_ID = 33;
    const COUNTRY_ID = 'US';
    const COUNTRY = 'United State';
    const POSTCODE = '49628-7978';
    const DESCRIPTION = 'To distribute products for brick-and-mortar store';
    const STOCK_ID = Stock::STOCK_ID;

    /**
     * @return array
     */
    static function LocationData(){
        return [
            LocationInterface::NAME => self::NAME,
            LocationInterface::STREET => self::STREET,
            LocationInterface::CITY => self::CITY,
            LocationInterface::REGION => self::REGION,
            LocationInterface::REGION_ID => self::REGION_ID,
            LocationInterface::COUNTRY_ID => self::COUNTRY_ID,
            LocationInterface::COUNTRY => self::COUNTRY,
            LocationInterface::POSTCODE => self::POSTCODE,
            LocationInterface::DESCRIPTION => self::DESCRIPTION,
            LocationInterface::STOCK_ID => self::STOCK_ID,
        ];
    }
}
