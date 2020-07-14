<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\Indexer;

class ProductPage extends AbstractIndexer
{
    const INDEXER_ID = 'amasty_xlanding_product_page';

    /**
     * @inheritdoc
     */
    protected function doExecuteRow($id)
    {
        $this->getIndexBuilder()->reindexByProductIds([$id]);
    }

    /**
     * @inheritdoc
     */
    protected function doExecuteList($ids)
    {
        $this->getIndexBuilder()->reindexByProductIds($ids);
    }

    /**
     * @return bool
     */
    protected function isUpdateOnSaveAvailable(): bool
    {
        return $this->data[static::FORCED_FLAG] ?? false;
    }
}
