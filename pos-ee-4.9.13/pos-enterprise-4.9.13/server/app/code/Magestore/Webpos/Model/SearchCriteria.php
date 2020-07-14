<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model;
use Magestore\Webpos\Api\SearchCriteriaInterface;

/**
 * Search Criteria
 */
class SearchCriteria extends \Magento\Framework\Api\SearchCriteria
    implements \Magestore\Webpos\Api\SearchCriteriaInterface
{
    const QUERY_STRING = 'query_string';
    const IS_LIMIT = 'is_limit';
    const IS_HOLD = 'is_hold';

    /**
     * @inheritDoc
     */
    public function setQueryString($queryString)
    {
        $this->setData(self::QUERY_STRING, $queryString);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getQueryString()
    {
        return $this->_get(self::QUERY_STRING);
    }

    /**
     * @inheritDoc
     */
    public function setIsLimit($isLimit)
    {
        $this->setData(self::IS_LIMIT, $isLimit);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIsLimit()
    {
        return $this->_get(self::IS_LIMIT);
    }

    /**
     * @inheritDoc
     */
    public function setIsHold($isHold)
    {
        $this->setData(self::IS_HOLD, $isHold);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIsHold()
    {
        return $this->_get(self::IS_HOLD);
    }
}
