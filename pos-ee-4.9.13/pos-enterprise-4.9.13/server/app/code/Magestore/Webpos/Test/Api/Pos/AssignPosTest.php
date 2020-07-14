<?php


namespace Magestore\Webpos\Test\Api\Pos;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Framework\Webapi\Rest\Request as RestRequest;
use Magento\TestFramework\Helper\Bootstrap;
/**
 * Class AssignPosTest
 * @package Magento\Webpos\Api\Pos
 */
class AssignPosTest extends WebapiAbstract
{
    const RESOURCE_PATH = '/V1/webpos/posassign';

    /**
     * @var $pos_id
     */
    protected $pos_id = 1;
    /**
     * @var $location_id
     */
    protected $location_id = 1;


    public function getAssignPosResponse($session_id)
    {
        $requestData = [
            'pos' => [
                'pos_id' => $this->pos_id,
                'location_id' => $this->location_id
            ],
        ];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH
                    .'?pos_session=' . $session_id,
                'httpMethod' => RestRequest::HTTP_METHOD_POST,
            ]
        ];
        return $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * Get Configuration
     */
    public function testPosAssign()
    {
        /**
         * @var \Magento\Webpos\Api\Staff\StaffTest $staffLogin
         */
        $staffLogin = Bootstrap::getObjectManager()->get('\Magestore\Webpos\Test\Api\Staff\LoginTest');
        $loginPosResult = $staffLogin->getLoginResponse();
        $assignPosResult = $this->getAssignPosResponse($loginPosResult['session_id']);

        self::assertContains(
            'message',
            array_keys($assignPosResult),
            "message key is not in found in result's keys. Response: 
            ". json_encode($assignPosResult, JSON_PRETTY_PRINT)
        );

        $this->assertEquals("Enter to pos successfully", $assignPosResult['message']);
    }
}