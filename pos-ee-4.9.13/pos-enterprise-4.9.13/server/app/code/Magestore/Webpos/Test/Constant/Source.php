<?php

/**
 * source simple Data
 */
namespace Magestore\Webpos\Test\Constant;
use Magento\InventoryApi\Api\Data\SourceInterface;

/**
 * Class Source
 * @package Magestore\Webpos\Test\Constant
 */
class Source
{
    const SOURCE_CODE           = 'source-code-1';
    const SOURCE_NAME           = 'source-name-1';
    const SOURCE_CONTACT_NAME   = 'source-contact-name';
    const SOURCE_EMAIL          = 'sourceemail@gmail.com';
    const SOURCE_DESCRIPTION    = 'source-description';
    const SOURCE_LATITUDE       = 11.123456;
    const SOURCE_LONGITUDE      = 12.123456;
    const SOURCE_COUNTRY_ID     = 'US';
    const SOURCE_REGION_ID      = 10;
    const SOURCE_CITY           = 'source-city';
    const SOURCE_STREET         = 'source-street';
    const SOURCE_POSTCODE       = 'source-postcode';
    const SOURCE_PHONE          = 123456789;
    const SOURCE_FAX            = 5551234;

    /**
     * @return array
     */
    static function sourceData(){
        return [
            SourceInterface::SOURCE_CODE => Source::SOURCE_CODE,
            SourceInterface::NAME => Source::SOURCE_NAME,
            SourceInterface::CONTACT_NAME => Source::SOURCE_CONTACT_NAME,
            SourceInterface::EMAIL => Source::SOURCE_EMAIL,
            SourceInterface::ENABLED => true,
            SourceInterface::DESCRIPTION => Source::SOURCE_DESCRIPTION,
            SourceInterface::LATITUDE => Source::SOURCE_LATITUDE,
            SourceInterface::LONGITUDE => Source::SOURCE_LONGITUDE,
            SourceInterface::COUNTRY_ID => Source::SOURCE_COUNTRY_ID,
            SourceInterface::REGION_ID => Source::SOURCE_REGION_ID,
            SourceInterface::CITY => Source::SOURCE_CITY,
            SourceInterface::STREET => Source::SOURCE_STREET,
            SourceInterface::POSTCODE => Source::SOURCE_POSTCODE,
            SourceInterface::PHONE => Source::SOURCE_PHONE,
            SourceInterface::FAX => Source::SOURCE_FAX,
            SourceInterface::USE_DEFAULT_CARRIER_CONFIG => 0,
            SourceInterface::USE_DEFAULT_CARRIER_CONFIG => false,
            SourceInterface::CARRIER_LINKS => [],
        ];
    }
}
