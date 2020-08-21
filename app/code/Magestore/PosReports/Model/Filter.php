<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Model;

use Magento\Framework\App\RequestInterface;

/**
 * Report filter model - use this to get filter params from request
 *
 * Class Filter
 */
class Filter
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var string
     */
    protected $filterParamName;

    /**
     * Filter constructor.
     * @param RequestInterface $request
     * @param string $filterParamName
     */
    public function __construct(
        RequestInterface $request,
        $filterParamName = "reportFilters"
    ) {
        $this->request = $request;
        $this->filterParamName = $filterParamName;
    }

    /**
     * Get filter
     *
     * @param string $key
     * @return mixed|string
     */
    public function getFilter($key = "")
    {
        $filters = $this->request->getParam($this->filterParamName);
        if ($key) {
            return (isset($filters[$key])) ? $filters[$key] : "";
        } else {
            return $filters;
        }
    }
}
