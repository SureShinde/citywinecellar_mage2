<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\Barcode;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Event barcode Save After
 */
class SaveAfter implements ObserverInterface
{

    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magestore\Webpos\Model\Indexer\Product\Processor
     */
    protected $indexProcessor;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * SaveAfter constructor.
     *
     * @param \Magestore\Webpos\Helper\Data $helper
     * @param \Magestore\Webpos\Model\Indexer\Product\Processor $indexProcessor
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magestore\Webpos\Helper\Data $helper,
        \Magestore\Webpos\Model\Indexer\Product\Processor $indexProcessor,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->helper = $helper;
        $this->indexProcessor = $indexProcessor;
        $this->objectManager = $objectManager;
    }

    /**
     * Execute
     *
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        $barcodeModel = $observer->getEvent()->getDataObject();
        /** @var \Magestore\BarcodeSuccess\Model\Barcode $barcode */
        $barcode = $this->objectManager->create(\Magestore\BarcodeSuccess\Model\Barcode::class);
        $barcode->load($barcodeModel->getId());

        if ($this->helper->isEnableElasticSearch()) {
            if ($this->indexProcessor->getIndexer()->isValid()) {
                $this->indexProcessor->reindexRow($barcode->getProductId());
            }
        }
    }
}
