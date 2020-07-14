<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Service\Request;

/**
 * Class SaveActionLog
 *
 * @package Magestore\Webpos\Model\Service\Request
 */
class SaveActionLog
{
    /**
     * @var array
     */
    protected $actionLogType;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * SaveActionLog constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $actionLogType
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $actionLogType = []
    ) {
        $this->objectManager = $objectManager;
        $this->actionLogType = $actionLogType;
    }

    /**
     * Save action log
     *
     * @param \Magento\Framework\Webapi\Rest\Request $request
     * @param \Magento\Webapi\Controller\Rest\Router\Route $route
     * @return mixed
     */
    public function execute(
        \Magento\Framework\Webapi\Rest\Request $request,
        \Magento\Webapi\Controller\Rest\Router\Route $route
    ) {
        switch ($route->getRoutePath()) {
            case 'V1/webpos/checkout/placeOrder':
                $actionType = "order";
                break;
            case 'V1/webpos/order/takePayment':
                $actionType = \Magestore\Webpos\Model\Request\Actions\TakePaymentAction::ACTION_TYPE;
                break;
            case 'V1/webposshipping/order/createShipment':
                $actionType = \Magestore\Webpos\Model\Request\Actions\ShipmentAction::ACTION_TYPE;
                break;
            case 'V1/webpos/creditmemos/create':
                $actionType = \Magestore\Webpos\Model\Request\Actions\RefundAction::ACTION_TYPE;
                break;
            case 'V1/webpos/order/cancel':
                $actionType = \Magestore\Webpos\Model\Request\Actions\CancelAction::ACTION_TYPE;
                break;
            default:
                $actionType = "";
        }

        if (!isset($this->actionLogType[$actionType]) || !$this->actionLogType[$actionType]) {
            return;
        }

        $process = $this->objectManager->create($this->actionLogType[$actionType]);
        $process->saveRequest($request);
    }
}
