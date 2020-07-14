<?php

namespace Magestore\Webpos\Test\Api\Staff;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class LoginTest
 * @package Magestore\Webpos\Test\Api\Staff
 */
class LoginTest extends WebapiAbstract
{
	const RESOURCE_PATH = '/V1/webpos/staff/login';

    /**
     * @var $username
     */
    protected $username = 'admin';
    /**
     * @var $password
     */
    protected $password = 'admin123';


    public function getLoginResponse()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_POST,
            ],
        ];
        $requestData = [
            'staff' => [
                'username' => $this->username,
                'password' => $this->password
            ],
        ];
        return $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * @var \Magento\Webpos\Api\Staff\Constraint\Staff
     */
    protected $staff;

    protected function setUp()
    {
        $this->staff = Bootstrap::getObjectManager()->get('\Magestore\Webpos\Test\Api\Staff\Expected\LoginResult');
    }

	public function testStaffLogin()
	{
		$result = $this->getLoginResponse();
		$this->assertNotNull($result);

        foreach ($this->staff->getSchema() as $key) {
            $this->assertArrayHasKey($key, $result);
        }
	}
}