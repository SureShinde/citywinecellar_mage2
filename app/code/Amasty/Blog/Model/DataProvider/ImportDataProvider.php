<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Amasty\Blog\Model\ResourceModel\Comments\CollectionFactory;

/**
 * Class ImportDataProvider
 */
class ImportDataProvider extends AbstractDataProvider
{
    /**
     * @return array
     */
    public function getData()
    {
        return [];
    }

    /**
     * @param \Magento\Framework\Api\Filter $filter
     * @return null
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        return null;
    }
}
