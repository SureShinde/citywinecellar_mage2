<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Indexer\Product;

class FlatTableBuilder extends \Magento\Catalog\Model\Indexer\Product\Flat\FlatTableBuilder
{
    public function __construct(
        \Magestore\Webpos\Helper\Product\Indexer $productIndexerHelper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\TableDataInterface $tableData
    ) {
        parent::__construct($productIndexerHelper, $resource, $config, $storeManager, $tableData);
    }
}
