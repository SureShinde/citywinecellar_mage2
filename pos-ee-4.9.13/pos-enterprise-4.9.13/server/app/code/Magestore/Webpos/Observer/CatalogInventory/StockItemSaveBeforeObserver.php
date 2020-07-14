<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Observer\CatalogInventory;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;


class StockItemSaveBeforeObserver implements ObserverInterface
{

    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;

    /**
     * StockItemSaveBeforeObserver constructor.
     * @param \Magestore\Webpos\Helper\Data $helper
     */
    public function __construct(
        \Magestore\Webpos\Helper\Data $helper
    )
    {
        $this->helper = $helper;
    }

    /**
     *
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        $object = $observer->getEvent()->getObject();
        if (
            !$this->helper->isEnabledInventory() &&
            ($object instanceof \Magento\CatalogInventory\Api\Data\StockItemInterface)
        ) {
            $object->setData('updated_time', null);
        }

    }

}