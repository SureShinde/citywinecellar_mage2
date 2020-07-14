<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Indexer\Product\Action;

class Full extends \Magento\Catalog\Model\Indexer\Product\Flat\Action\Full
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Webpos\Helper\Product\Indexer $productHelper,
        \Magento\Catalog\Model\Product\Type $productType,
        \Magestore\Webpos\Model\Indexer\Product\TableBuilder $tableBuilder,
        \Magestore\Webpos\Model\Indexer\Product\FlatTableBuilder $flatTableBuilder
    ) {
        $this->_storeManager = $storeManager;
        $this->_productIndexerHelper = $productHelper;
        $this->_productType = $productType;
        $this->_connection = $resource->getConnection();
        $this->_tableBuilder = $tableBuilder;
        $this->_flatTableBuilder = $flatTableBuilder;
    }
}
