<?php

/**
 * delete source
 */

use Magento\Framework\App\ResourceConnection;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magestore\Webpos\Test\Constant\Source;

/** @var ResourceConnection $connection */
$connection = Bootstrap::getObjectManager()->get(ResourceConnection::class);
$connection->getConnection()->delete($connection->getTableName('inventory_source'), [
    SourceInterface::SOURCE_CODE . ' IN (?)' => [Source::SOURCE_CODE]
]);
