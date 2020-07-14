<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Cron;

use Psr\Log\LoggerInterface;
use Magento\Indexer\Model\Indexer;

/**
 * Class LandingPagesReindex
 * @package Amasty\Xlanding\Cron
 */
class LandingPagesReindex
{
    const CATALOGSEARCH_INDEXER_ID = 'catalogsearch_fulltext';
    const PRODUCT_ATTRIBUTES_INDEXER_ID = 'catalog_product_attribute';
    const LANDING_PAGE_INDEXER_ID = 'amasty_xlanding_page_product';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Indexer
     */
    private $indexer;

    public function __construct(
        LoggerInterface $logger,
        Indexer $indexer
    ) {
        $this->logger = $logger;
        $this->indexer = $indexer;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $indexers = [
            self::PRODUCT_ATTRIBUTES_INDEXER_ID,
            self::LANDING_PAGE_INDEXER_ID,
            self::CATALOGSEARCH_INDEXER_ID
        ];
        try {
            foreach ($indexers as $indexer) {
                $this->indexer->load($indexer);
                $this->indexer->reindexAll();
            }
        } catch (\Exception $e) {
            $this->logError($e);
        }
    }

    /**
     * @param \Exception $e
     */
    private function logError(\Exception $e)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/amasty_landing_reindex_error.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
}
