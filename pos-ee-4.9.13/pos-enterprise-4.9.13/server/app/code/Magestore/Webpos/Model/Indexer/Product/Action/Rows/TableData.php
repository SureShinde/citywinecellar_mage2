<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Indexer\Product\Action\Rows;

class TableData extends \Magento\Catalog\Model\Indexer\Product\Flat\Action\Rows\TableData
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magestore\Webpos\Helper\Product\Indexer $productIndexerHelper
    ) {
        parent::__construct($resource, $productIndexerHelper);
    }
}
