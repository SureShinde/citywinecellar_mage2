<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api;

/**
 * @api
 */
interface SearchCriteriaInterface extends \Magento\Framework\Api\SearchCriteriaInterface
{
    /**
     * Get query string
     * 
     * @return string
     */
    public function getQueryString();
    
    /**
     * Set query string
     * 
     * @param string $queryString
     * @return SearchCriteriaInterface
     */
    public function setQueryString($queryString);

    /**
     * Get is limit
     *
     * @return int|null
     */
    public function getIsLimit();

    /**
     * Set is limit
     *
     * @param int|null $isLimit
     * @return SearchCriteriaInterface
     */
    public function setIsLimit($isLimit);

    /**
     * Get is hold
     *
     * @return int|null
     */
    public function getIsHold();

    /**
     * Set is hold
     *
     * @param int|null $isHold
     * @return SearchCriteriaInterface
     */
    public function setIsHold($isHold);
}
