<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\TransferStock\Ui\Component\Form\Fieldset;

use Magento\Ui\Component\Form\Fieldset;
use Magestore\TransferStock\Model\InventoryTransfer\Option\Stage;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magestore\TransferStock\Model\InventoryTransfer\Option\Status;

/**
 * Class Websites Fieldset
 */
class ShortFall extends Fieldset
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * ReceiveHistory constructor.
     * @param ContextInterface $context
     * @param \Magento\Framework\Registry $registry
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        \Magento\Framework\Registry $registry,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->registry = $registry;
        $this->context = $context;
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        /** @var \Magestore\TransferStock\Model\InventoryTransfer $transfer */
        if ($transfer = $this->registry->registry('current_inventory_transfer')) {
            if(!$transfer->getInventorytransferId()
                || $transfer->getStage() == Stage::STAGE_COMPLETED
                || $transfer->getStatus() == Status::STATUS_OPEN) {
                $this->_data['config']['componentDisabled'] = true;
            }
        }
    }
}
