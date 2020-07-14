<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Indexer\Product;

use Magento\Catalog\Model\Indexer\Product\Flat\Table\BuilderInterfaceFactory;

class TableBuilder extends \Magento\Catalog\Model\Indexer\Product\Flat\TableBuilder
{
    public function __construct(
        \Magestore\Webpos\Helper\Product\Indexer $productIndexerHelper,
        \Magento\Framework\App\ResourceConnection $resource,
        BuilderInterfaceFactory $tableBuilderFactory = null
    ) {
        parent::__construct($productIndexerHelper, $resource, $tableBuilderFactory);
    }
}
