<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Block\Adminhtml\InventoryTransfer\Edit\Steps;

use Magestore\TransferStock\Model\InventoryTransfer\Option\Stage;


/**
 * Class Timeline
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Steps
 */
class Timeline extends  \Magento\Ui\Block\Component\StepsWizard
{
    /**
     * Wizard step template
     *
     * @var string
     */
    protected $_template = 'Magestore_TransferStock::form/timeline.phtml';

    /**
     * @var null|\Magento\Ui\Block\Component\StepsWizard\StepInterface[]
     */
    private $steps;
    
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Timeline constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->registry = $registry;
    }

    /**
     * Wizard step template
     *
     * @var string
     */
    public function getCurrentStep()
    {
        $stage = $this->getCurrentStage();
        switch ($stage) {
            case Stage::STAGE_COMPLETED :
                $step = 'variation-steps-wizard_completed';
                break;
            case Stage::STAGE_RECEIVING :
                $step = 'variation-steps-wizard_receiving';
                break;
            case Stage::STAGE_SENT :
                $step = 'variation-steps-wizard_sent';
                break;
            default:
                $step = 'variation-steps-wizard_new';
                break;
        }
        return $step;
    }

    /**
     * @return \Magento\Ui\Block\Component\StepsWizard\StepInterface[]
     */
    public function getSteps()
    {
        if ($this->steps === null) {
            foreach ($this->getLayout()->getChildBlocks($this->getNameInLayout()) as $step) {
                if ($step instanceof \Magento\Ui\Block\Component\StepsWizard\StepInterface) {
                    $this->steps[$step->getComponentName()] = $step;
                }
            }
        }
        return $this->steps;
    }
    
    public function getCurrentStage(){
        $stage = '';
        $inventoryTransfer = $this->registry->registry('current_inventory_transfer');
        if(isset($inventoryTransfer) && $inventoryTransfer->getId())
            $stage = $inventoryTransfer->getStage();
        return $stage;
    }
}