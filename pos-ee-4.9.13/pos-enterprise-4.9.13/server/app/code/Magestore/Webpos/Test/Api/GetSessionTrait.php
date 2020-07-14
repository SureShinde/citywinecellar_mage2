<?php

declare(strict_types=1);

namespace Magestore\Webpos\Test\Api;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Webapi\Rest\Request as RestRequest;
use Magento\InventoryApi\Api\Data\StockInterface;
use Magento\TestFramework\Assert\AssertArrayContains;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Webapi\Exception;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\App\ResourceConnection;
use Magento\InventoryApi\Api\Data\SourceInterface;

use Magento\Mtf\Fixture\FixtureInterface;
use Magestore\Webpos\Test\Constant\Product;

/**
 * Trait GetSessionTrait
 * @package Magestore\Webpos\Test\Api
 */
trait GetSessionTrait
{

    /**
     * @var
     */
    protected $testCaseId;
    protected $messageTxt = "Message";
    protected $statusCodeTxt = "StatusCode";
    protected $parametersTxt = "Parameters";

    /**
     * get pos_session
     * @return $sessionId
     */
    public function loginAndAssignPos()
    {
        /**
         * @var \Magestore\Webpos\Test\Api\Staff\LoginTest $staffLogin
         */
        $staffLogin = Bootstrap::getObjectManager()->get('\Magestore\Webpos\Test\Api\Staff\LoginTest');
        $loginPosResult = $staffLogin->getLoginResponse();
        $posAssign = Bootstrap::getObjectManager()->get('\Magestore\Webpos\Test\Api\Pos\AssignPosTest');
        $assignPosResult = $posAssign->getAssignPosResponse($loginPosResult['session_id']);
        $this->assertEquals("Enter to pos successfully", $assignPosResult['message']);
        $sessionId = $loginPosResult['session_id'];
        return $sessionId;
    }

    /**
     * get response data from API test
     * @param $requestData
     * @param $session_id
     * @return $response
     */
    public function getResponseAPI($requestData = null, $session_id = false)
    {
        if (!$session_id) {
            $session_id = $this->posSession;
        }
        $query = ($requestData) ? '?'.http_build_query($requestData) : '';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . $query
                    .'&show_option=1'
                    .'&pos_session=' . $session_id,
                'httpMethod' => RestRequest::HTTP_METHOD_GET,
            ]
        ];

        if (!$query) {
            $serviceInfo = [
                'rest' => [
                    'resourcePath' => self::RESOURCE_PATH
                        .'?pos_session=' . $session_id,
                    'httpMethod' => RestRequest::HTTP_METHOD_GET,
                ]
            ];
        }

        if ($requestData) {
            $response = $this->_webApiCall($serviceInfo, $requestData);
        } else {
            $response = $this->_webApiCall($serviceInfo);
        }
        return $response;
    }

    /**
     * @param $requestData
     * @return mixed
     */
    public function getResponseAPIwithoutSession($requestData)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?'.http_build_query($requestData)
                    .'&show_option=1',
                'httpMethod' => RestRequest::HTTP_METHOD_GET,
            ]
        ];
        $response = $this->_webApiCall($serviceInfo, $requestData);
        return $response;
    }


    /**
     * @return mixed
     */
    public function getTimeBeforeCreated()
    {
        /** @var ResourceConnection $connection */
        $connection = Bootstrap::getObjectManager()->get(ResourceConnection::class);
        $lastTimeUPdated = $connection->getConnection()->fetchRow(
            $connection->getConnection()->select()
                ->from($connection->getTableName('catalog_product_entity'))
                ->where('sku = ?', Product::SKU_1)
        )['updated_at'];
        return $lastTimeUPdated;
    }

    /**
     * @return mixed
     */
    public function getTimeAfterCreatedAndUpdated()
    {
        /** @var ResourceConnection $connection */
        $connection = Bootstrap::getObjectManager()->get(ResourceConnection::class);
        $lastTimeUPdated = $connection->getConnection()->fetchRow(
            $connection->getConnection()->select()
                ->from($connection->getTableName('catalog_product_entity'))
                ->where('sku = ?', Product::SKU_10)
        )['updated_at'];
        return $lastTimeUPdated;
    }

    /**
     * get time after created all sample data to search
     * @return mixed
     */
    public function getTimeAfterCreated()
    {
        /** @var ResourceConnection $connection */
        $connection = Bootstrap::getObjectManager()->get(ResourceConnection::class);
        $lastTimeUPdated = $connection->getConnection()->fetchRow(
            $connection->getConnection()->select()
                ->from($connection->getTableName('catalog_product_entity'))
                ->where('sku = ?', Product::SKU_15)
        )['updated_at'];
        return $lastTimeUPdated;
    }

    /**
     * get time at first update sample data to search
     * @return mixed
     */
    public function getTimeAtFirstUpdated()
    {
        /** @var ResourceConnection $connection */
        $connection = Bootstrap::getObjectManager()->get(ResourceConnection::class);
        $lastTimeUPdated = $connection->getConnection()->fetchRow(
            $connection->getConnection()->select()
                ->from($connection->getTableName('catalog_product_entity'))
                ->where('sku = ?', Product::SKU_8)
        )['updated_at'];
        /*$query = "select updated_at from {$connection->getTableName('catalog_product_entity')} where sku ='SKU-8' ";
        $query2 = "select * from {$connection->getTableName('catalog_product_entity')} "
            . "where updated_at ='{$lastTimeUPdated}' ";
        $a = $connection->getConnection()->fetchAll($query);
        $b = $connection->getConnection()->fetchAll($query2);
        var_dump($lastTimeUPdated);
        var_dump($a);
        var_dump($b);*/
        return $lastTimeUPdated;
    }

    /**
     * @param $testCaseId
     * @param $message
     * @return string
     */
    public function getMessage($message)
    {
        return sprintf(
            'API "%s" fail at testcase "%s" - "%s" is invalid',
            $this->apiName,
            $this->testCaseId,
            $message
        );
    }

    /**
     * Test Case - the pos_session is not valid
     */
    public function sessionCase1()
    {
        $requestData = [
            'searchCriteria' => [
                SearchCriteria::FILTER_GROUPS => [
                    [
                        'filters' => [
                            [
                                'field' => 'sku',
                                'value' => Product::SKU_1,
                                'condition_type' => 'in',
                            ],
                        ],
                    ],
                ],
                SearchCriteria::PAGE_SIZE => 10,
                SearchCriteria::CURRENT_PAGE => 1
            ],
        ];
        $pos_session = 'hca98erfksahrfkj3hy4r89ashfkhsdf98';
        $expectedMessage = sprintf('Session with id "%s" does not exist.', $pos_session);
        try {
            $this->getResponseAPI($requestData, $pos_session);
            $this->fail('Expected throwing exception');
        } catch (\Exception $e) {
            $errorData = $this->processRestExceptionResult($e);

            /* check error message */
            $message = $this->getMessage($this->messageTxt);
            self::assertEquals($expectedMessage, $errorData['message'], $message);
            /* check error code */
            $message = $this->getMessage($this->statusCodeTxt);
            self::assertEquals(Exception::HTTP_UNAUTHORIZED, $e->getCode(), $message);
        }
    }

    /**
     * Test Case - the pos_session is missing
     */
    public function sessionCase2()
    {
        $requestData = [
            'searchCriteria' => [
                SearchCriteria::FILTER_GROUPS => [
                    [
                        'filters' => [
                            [
                                'field' => 'sku',
                                'value' => Product::SKU_1,
                                'condition_type' => 'in',
                            ],
                        ],
                    ],
                ],
                SearchCriteria::PAGE_SIZE => 10,
                SearchCriteria::CURRENT_PAGE => 1
            ],
        ];
        $expectedMessage = 'Session with id "" does not exist.';
        try {
            $this->getResponseAPIwithoutSession($requestData);
            $this->fail('Expected throwing exception');
        } catch (\Exception $e) {
            $errorData = $this->processRestExceptionResult($e);
            /* check error message */
            $message = $this->getMessage($this->messageTxt);
            self::assertEquals($expectedMessage, $errorData['message'], $message);
            /* check error code */
            $message = $this->getMessage($this->statusCodeTxt);
            self::assertEquals(Exception::HTTP_UNAUTHORIZED, $e->getCode(), $message);
        }
    }

    /**
     * Test Case  - the searchCriteria is missing
     */
    public function sessionCase3()
    {
        $expectedMessage = '"%fieldName" is required. Enter and try again.';
        $parameters = 'searchCriteria';

        try {
            $this->getResponseAPI();
            $this->fail('Expected throwing exception');

        } catch (\Exception $e) {
            $errorData = $this->processRestExceptionResult($e);

            /* check error message */
            $message = $this->getMessage($this->messageTxt);
            self::assertEquals($expectedMessage, $errorData['message'], $message);
            /* check error code */
            $message = $this->getMessage($this->statusCodeTxt);
            self::assertEquals(Exception::HTTP_BAD_REQUEST, $e->getCode(), $message);
            /* check parameters */
            $message = $this->getMessage($this->parametersTxt);
            self::assertEquals($parameters, $errorData['parameters']['fieldName'], $message);
        }
    }
}
