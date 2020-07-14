<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Model;

/**
 * Class Context
 * @package Magestore\PurchaseOrderSuccess\Model
 */
class Context extends \Magento\Framework\Model\Context
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Event\ManagerInterface $eventDispatcher,
        \Magento\Framework\App\CacheInterface $cacheManager,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Model\ActionValidator\RemoveAction $actionValidator
    )
    {
        parent::__construct($logger, $eventDispatcher, $cacheManager, $appState, $actionValidator);
    }
}