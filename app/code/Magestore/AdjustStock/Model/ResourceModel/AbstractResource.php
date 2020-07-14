<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;

/**
 *
 *
 * @category Magestore
 * @package  Magestore_AdjustStock
 * @module   AdjustStock
 * @author   Magestore Developer
 */
abstract class AbstractResource extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     *
     * @var \Magestore\AdjustStock\Api\Db\QueryProcessorInterface
     */
    protected $_queryProcessor;

    /**
     * AbstractResource constructor.
     * @param \Magestore\AdjustStock\Api\Db\QueryProcessorInterface $queryProcessor
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param null $connectionName
     */
    public function __construct(
        \Magestore\AdjustStock\Api\Db\QueryProcessorInterface $queryProcessor,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    )
    {
        $this->_queryProcessor = $queryProcessor;
        parent::__construct($context, $connectionName);
    }

    /**
     * insert data to table.
     *
     * @param $table
     * @param array $data
     *
     * @throws LocalizedException
     */
    public function insertData($table, array $data = [])
    {
        if (empty($data)) {
            return;
        }

        $connection = $this->getConnection();
        $connection->beginTransaction();
        try {
            $connection->insertMultiple($table, $data);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw new LocalizedException(__($e->getMessage()), $e);
        }
    }

    /**
     * delete data from table.
     *
     * @param $table
     * @param array $where
     *
     * @throws LocalizedException
     */
    public function deleteData($table, array $where = [])
    {
        if (empty($where)) {
            return;
        }

        $connection = $this->getConnection();
        $connection->beginTransaction();
        try {
            $connection->delete($table, $where);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw new LocalizedException(__($e->getMessage()), $e);
        }
    }

    /**
     * update data for table.
     *
     * @param $table
     * @param $bind
     * @param $where
     *
     * @throws LocalizedException
     */
    public function updateData($table, $bind, $where = [])
    {
        if (empty($where)) {
            return;
        }

        $connection = $this->getConnection();
        $connection->beginTransaction();
        try {
            $connection->update($table, $bind, $where);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw new LocalizedException(__($e->getMessage()), $e);
        }
    }
}
