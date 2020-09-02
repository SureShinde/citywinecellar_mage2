<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Block\Adminhtml\Order\View;

/**
 * Class Batch
 * @package Magestore\OrderSuccess\Block\Adminhtml\Sales\View
 */
class Batch extends \Magestore\OrderSuccess\Block\Adminhtml\Order\View\Info
{

    /**
     * @return \Magento\Framework\App\State
     */
    public function getBatchList()
    {
        return $this->batchSourceInterface->toOptionArray();
    }

    /**
     * @return string
     */
    public function getCurrentBatch()
    {
        $order = $this->getOrder();
        $batch = $order->getBatchId();
        return $batch;
    }

}

