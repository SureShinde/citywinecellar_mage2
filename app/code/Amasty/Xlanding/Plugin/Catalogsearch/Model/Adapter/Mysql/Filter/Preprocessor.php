<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Plugin\Catalogsearch\Model\Adapter\Mysql\Filter;

use Magento\Framework\Search\Request\FilterInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Preprocessor
{
    const LANDING_FILTER_NAME = 'landing_page_id_filter';
    const LANDING_FILTER_FIELD = 'page_id';
    const FILTER_SUFFIX = '_filter';

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    public function __construct(ResourceConnection $resource)
    {
        $this->connection = $resource->getConnection();
    }

    /**
     * @param $subject
     * @param callable $proceed
     * @param FilterInterface $filter
     * @param $isNegation
     * @param $query
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormatParameter)
     */
    public function aroundProcess(
        $subject,
        callable $proceed,
        FilterInterface $filter,
        $isNegation,
        $query
    ) {
        if ($filter->getName() === self::LANDING_FILTER_NAME) {
            $newFieldName = $filter->getField() . self::FILTER_SUFFIX . '.' . self::LANDING_FILTER_FIELD;
            return str_replace(
                $this->connection->quoteIdentifier($filter->getField()),
                $this->connection->quoteIdentifier($newFieldName),
                $query
            );
        }

        return $proceed($filter, $isNegation, $query);
    }
}
