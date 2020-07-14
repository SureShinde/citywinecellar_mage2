<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\Indexer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;

abstract class AbstractIndexer implements IndexerActionInterface, MviewActionInterface
{
    /**
     * Used for determine if Update on save available in this moment
     */
    const FORCED_FLAG = 'forced';

    /**
     * @var IndexBuilder
     */
    private $indexBuilder;

    /**
     * @var array
     */
    protected $data = [];

    public function __construct(
        IndexBuilder $indexBuilder,
        array $data = []
    ) {
        $this->indexBuilder = $indexBuilder;
        $this->data = $data;
    }

    /**
     * @param int[] $ids
     * @throws LocalizedException
     */
    public function execute($ids)
    {
        $this->executeList($ids);
    }

    /**
     * @throws LocalizedException
     */
    public function executeFull()
    {
        $this->indexBuilder->reindexFull();
    }

    /**
     * @param int $id
     * @throws LocalizedException
     */
    public function executeRow($id)
    {
        if ($this->isUpdateOnSaveAvailable()) {
            if (!$id) {
                throw new LocalizedException(
                    __('We can\'t rebuild the index for an undefined entity.')
                );
            }

            $this->doExecuteRow($id);
        }
    }

    /**
     * @param int[] $ids
     * @throws LocalizedException
     */
    public function executeList(array $ids)
    {
        if ($this->isUpdateOnSaveAvailable()) {
            if (!$ids) {
                throw new LocalizedException(
                    __('Could not rebuild index for empty products array')
                );
            }
            $this->doExecuteList($ids);
        }
    }

    /**
     * @param int $id
     */
    abstract protected function doExecuteRow($id);

    /**
     * @param int[] $ids
     */
    abstract protected function doExecuteList($ids);

    /**
     * @return IndexBuilder
     */
    protected function getIndexBuilder()
    {
        return $this->indexBuilder;
    }

    /**
     * Use this check for avoid multiple reindex after product save
     *
     * @return bool
     */
    abstract protected function isUpdateOnSaveAvailable(): bool;
}
