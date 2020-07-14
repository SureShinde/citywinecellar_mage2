<?php

declare(strict_types=1);

namespace Magestore\Webpos\Test\Api\DeletedProducts;

use Magento\Framework\Api\SearchCriteria;
use Magento\TestFramework\Assert\AssertArrayContains;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Webapi\Exception;
use Magento\Mtf\Fixture\FixtureInterface;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\App\ResourceConnection;
use Magento\InventoryApi\Api\Data\SourceInterface;

/**
 * Trait DeletedProductsTrait
 * @package Magestore\Webpos\Test\Api\DeletedProducts
 */
trait DeletedProductsTrait
{

    /**
     * @var $requestData
     */
    protected $requestData;

    /**
     * @var
     */
    protected $queryString;

    /**
     * @param null $queryString
     * @param null $pageSize
     * @param null $currentPage
     * @return array
     */
    public function createRequestData($time, $gt = false){
        $requestData = [
            'searchCriteria' => [
                SearchCriteria::FILTER_GROUPS => [
                    [
                        'filters' => [
                            [
                                'field' => 'updated_at',
                                'value' => $time,
                                'condition_type' => !$gt ? 'gteq' : 'gt',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        return $this->requestData = $requestData;
    }

    /**
     * @return bool
     */
    public function deleteTable(){
        try {
            /** @var ResourceConnection $connection */
            $connection = Bootstrap::getObjectManager()->get(ResourceConnection::class);
            $connection->getConnection()->delete($connection->getTableName('webpos_product_deleted'));
            return true;
        }catch (\Exception $exception){
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function updateTimeForNextTestCase(){
        /** @var ResourceConnection $connection */
        $connection = Bootstrap::getObjectManager()->get(ResourceConnection::class);
        $lastTimeUPdated = $connection->getConnection()->fetchRow(
            $connection->getConnection()->select()
                ->from($connection->getTableName('webpos_product_deleted'))
                ->order('deleted_at DESC')
                //->where('sku = ?', 'SKU-15')
                ->limit(1)
        )['deleted_at'];
        return $this->time = $lastTimeUPdated;
    }

}
