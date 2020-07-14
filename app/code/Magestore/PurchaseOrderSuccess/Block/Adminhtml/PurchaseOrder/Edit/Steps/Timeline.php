<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Steps;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;

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
    protected $_template = 'Magestore_PurchaseOrderSuccess::purchaseorder/form/timeline.phtml';

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
        $step = 'variation-steps-wizard_new';
        $status = $this->getCurrentStatus();
        switch ($status) {
            case Status::STATUS_CANCELED :
                $step = 'variation-steps-wizard_canceled';
                break;
            case Status::STATUS_COMPLETED :
                $step = 'variation-steps-wizard_completed';
                break;
            case Status::STATUS_PROCESSING :
                $step = 'variation-steps-wizard_processing';
                break;
            case Status::STATUS_PENDING :
                $step = 'variation-steps-wizard_pending';
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
                    if(strpos($step->getComponentName(), 'wizard_canceled') !== false   ){
                        if($this->getCurrentStatus()!=Status::STATUS_CANCELED)
                            continue;
                    }
                    $this->steps[$step->getComponentName()] = $step;
                }
            }
        }
        return $this->steps;
    }
    
    public function getCurrentStatus(){
        $status = '';
        $purchaseOrder = $this->registry->registry('current_purchase_order');
        if(isset($purchaseOrder) && $purchaseOrder->getId())
            $status = $purchaseOrder->getStatus();
        return $status;
    }

    /**
     * 
     * @return string|null
     */
    public function toHtml()
    {
        $purchaseOrder = $this->registry->registry('current_purchase_order');
        if(isset($purchaseOrder) && $purchaseOrder->getType() == Type::TYPE_QUOTATION) {
            return null;
        }
        if($this->_request->getParam('type') == Type::TYPE_QUOTATION) {
            return null;
        }
        return parent::toHtml();
    }
}