<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Model\Event;

/**
 * Class DispatchService
 * @package Magestore\Appadmin\Model\Event
 */
class DispatchService implements \Magestore\Appadmin\Api\Event\DispatchServiceInterface
{
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * DispatchService constructor.
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->eventManager = $eventManager;
    }

    /**
     * @inheritdoc
     */
    public function dispatchEventForceSignOut($staffId, $posId = null){
        $this->eventManager->dispatch(self::EVENT_NAME_FORCE_SIGN_OUT, ['staff_id' => $staffId, 'pos_id' => $posId]);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function dispatchEventForceChangePos($staffId, $posId){
        $this->eventManager->dispatch(self::EVENT_NAME_FORCE_CHANGE_POS, ['staff_id' => $staffId, 'pos_id' => $posId]);
        return true;
    }
}