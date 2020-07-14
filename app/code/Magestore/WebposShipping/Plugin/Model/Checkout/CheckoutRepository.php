<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposShipping\Plugin\Model\Checkout;

/**
 * Class CheckoutRepository
 *
 * @package Magestore\WebposShipping\Plugin\Model\Checkout
 */
class CheckoutRepository
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
    /**
     * @var \Magestore\WebposShipping\Model\Order\ShippingService
     */
    protected $shippingService;
    /**
     * @var \Magestore\Webpos\Model\Sales\OrderRepository
     */
    protected $orderRepository;
    /**
     *
     * @var \Magestore\Webpos\Helper\Order
     */
    protected $orderHelper;

    /**
     * CheckoutRepository constructor.
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magestore\WebposShipping\Model\Order\ShippingService $shippingService
     * @param \Magestore\Webpos\Model\Sales\OrderRepository $orderRepository
     * @param \Magestore\Webpos\Helper\Order $orderHelper
     */
    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magestore\WebposShipping\Model\Order\ShippingService $shippingService,
        \Magestore\Webpos\Model\Sales\OrderRepository $orderRepository,
        \Magestore\Webpos\Helper\Order $orderHelper
    ) {
        $this->moduleManager = $moduleManager;
        $this->request = $request;
        $this->shippingService = $shippingService;
        $this->orderRepository = $orderRepository;
        $this->orderHelper = $orderHelper;
    }

//    /**
//     *  Add comment to shipment, which is created bt POS
//     *
//     * @param \Magestore\Webpos\Api\Checkout\CheckoutRepositoryInterface $subject
//     * @param \Magestore\Webpos\Api\Data\Checkout\OrderInterface $result
//     * @param \Magestore\Webpos\Api\Data\Checkout\OrderInterface $order
//     * @param int $create_shipment
//     * @param int $create_invoice
//     * @return \Magestore\Webpos\Api\Data\Checkout\OrderInterface
//     * @throws \Exception
//     */
//    public function afterPlaceOrder(
//        \Magestore\Webpos\Api\Checkout\CheckoutRepositoryInterface $subject,
//        \Magestore\Webpos\Api\Data\Checkout\OrderInterface $result,
//        \Magestore\Webpos\Api\Data\Checkout\OrderInterface $order,
//        $create_shipment,
//        $create_invoice
//    )
//    {
//        if (!$create_shipment) {
//            return $result;
//        }
//
//        if ($result->getShippingMethod() === 'webpos_shipping_storepickup') {
//            return $result;
//        }
//        /** @var \Magento\Sales\Api\Data\OrderInterface $magentoOrder */
//        $magentoOrder = $this->orderRepository->getById($result->getEntityId());
//        $shipment = $magentoOrder->getShipmentsCollection()->getFirstItem();
//
//        if (!$shipment->getId()) {
//            return $this->orderHelper->verifyOrderReturn($this->orderRepository->get($result->getEntityId()));
//        }
//
//        $this->shippingService->addNote(__("A shipment was created on POS"), $magentoOrder, $shipment);
//        return $this->orderHelper->verifyOrderReturn($this->orderRepository->get($result->getEntityId()));
//    }
//    /**
//     *  Check FulfilSuccess is enable and perform skip create pick request skipping
//     *
//     * @param \Magestore\Webpos\Api\Checkout\CheckoutRepositoryInterface $subject
//     * @param \Magestore\Webpos\Api\Data\Checkout\OrderInterface $order
//     * @param int $create_shipment
//     * @param int $create_invoice
//     * @return array
//     */
//    public function beforePlaceOrder(
//        \Magestore\Webpos\Api\Checkout\CheckoutRepositoryInterface $subject,
//        \Magestore\Webpos\Api\Data\Checkout\OrderInterface $order,
//        $create_shipment,
//        $create_invoice
//    )
//    {
//        if (!$create_shipment) {
//            return [$order, $create_shipment, $create_invoice];
//        }
//
//        $hasFulfil = $this->moduleManager->isEnabled('Magestore_FulfilSuccess');
//
//        if (!$hasFulfil) {
//            return [$order, $create_shipment, $create_invoice];
//        }
//
//        $params = $this->request->getParams();
//        $params['skip_check_picking'] = true;
//        $this->request->setParams($params);
//
//    }
}
