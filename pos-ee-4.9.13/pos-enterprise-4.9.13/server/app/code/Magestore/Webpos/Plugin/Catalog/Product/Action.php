<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magestore\Webpos\Plugin\Catalog\Product;

/**
 * Class \Magestore\Webpos\Plugin\Catalog\Product\Action
 */
class Action
{
    /**
     * @var \Magestore\Webpos\Model\Indexer\Product
     */
    protected $webposIndexer;

    /**
     * Action constructor.
     *
     * @param \Magestore\Webpos\Model\Indexer\Product $webposIndexer
     */
    public function __construct(
        \Magestore\Webpos\Model\Indexer\Product $webposIndexer
    ) {
        $this->webposIndexer = $webposIndexer;
    }

    /**
     * After update attributes, reindex pos search
     *
     * @param \Magento\Catalog\Model\Product\Action $subject
     * @param \Magento\Catalog\Model\Product\Action $result
     * @param array $productIds
     * @param array $attrData
     * @param int $storeId
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterUpdateAttributes(
        \Magento\Catalog\Model\Product\Action $subject,
        $result,
        $productIds,
        $attrData,
        $storeId
    ) {
        $this->webposIndexer->executeList($productIds);
        return $result;
    }
}
