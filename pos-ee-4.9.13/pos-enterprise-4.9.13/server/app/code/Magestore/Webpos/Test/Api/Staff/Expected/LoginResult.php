<?php

namespace Magestore\Webpos\Test\Api\Staff\Expected;

use Magestore\Webpos\Test\Api\Staff\Expected\ConstraintAbstract as ConstraintAbstract;

/**
 * Class LoginResult
 * @package Magestore\Webpos\Test\Api\Staff\Expected
 */
class LoginResult extends ConstraintAbstract
{
    public function getSchemaJson()
    {
        return '
            {
              "staff_id": 0,
              "timeout": 0,
              "session_id": "string",
              "message": "string",
              "staff_name": "string",
              "locations": [
                {
                  "location_id": 0,
                  "name": "string",
                  "location_code": "string",
                  "address": {
                    "street": "string",
                    "city": "string",
                    "region": {
                      "region_code": "string",
                      "region": "string",
                      "region_id": 0,
                      "extension_attributes": {}
                    },
                    "region_id": 0,
                    "country_id": "string",
                    "country": "string",
                    "postcode": "string"
                  },
                  "telephone": "string",
                  "store_id": 0,
                  "pos": [
                    {
                      "pos_id": 0,
                      "pos_name": "string",
                      "staff_id": 0,
                      "status": 0,
                      "staff_name": "string"
                    }
                  ]
                }
              ],
              "website_id": 0,
              "sharing_account": 0,
              "token": "string"
            }
        ';
    }
}