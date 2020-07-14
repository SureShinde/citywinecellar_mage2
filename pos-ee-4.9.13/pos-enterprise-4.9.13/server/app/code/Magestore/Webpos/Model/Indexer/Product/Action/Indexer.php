<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Indexer\Product\Action;

class Indexer extends \Magento\Catalog\Model\Indexer\Product\Flat\Action\Indexer
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magestore\Webpos\Helper\Product\Indexer $productHelper
    ) {
        parent::__construct($resource, $productHelper);
    }
}
