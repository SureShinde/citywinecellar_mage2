<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Indexer\Product\Action;

class Eraser extends \Magento\Catalog\Model\Indexer\Product\Flat\Action\Eraser
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magestore\Webpos\Helper\Product\Indexer $productHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($resource, $productHelper, $storeManager);
    }
}
