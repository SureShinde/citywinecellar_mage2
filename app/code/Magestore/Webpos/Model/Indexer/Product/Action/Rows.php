<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Indexer\Product\Action;

class Rows extends \Magento\Catalog\Model\Indexer\Product\Flat\Action\Rows
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Webpos\Helper\Product\Indexer $productHelper,
        \Magento\Catalog\Model\Product\Type $productType,
        \Magestore\Webpos\Model\Indexer\Product\TableBuilder $tableBuilder,
        \Magestore\Webpos\Model\Indexer\Product\FlatTableBuilder $flatTableBuilder,
        Eraser $flatItemEraser
    ) {
        parent::__construct($resource, $storeManager, $productHelper, $productType, $tableBuilder, $flatTableBuilder, $flatItemEraser);
    }
}
