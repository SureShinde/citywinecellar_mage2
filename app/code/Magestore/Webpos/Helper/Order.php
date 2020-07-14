<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Helper;

use Magento\Framework\App\Helper\Context;
use Magestore\Webpos\Model\ResourceModel\Sales\Order\Collection;

/**
 * Class Data
 * @package Magestore\Webpos\Helper
 */
class Order extends \Magestore\Webpos\Helper\Data
{
    const PERMISSION_ALL = 'Magestore_Appadmin::all';
    const PERMISSION_MANAGE_ORDER = 'Magestore_Webpos::manage_order';
    const PERMISSION_CREATED_AT_LOCATION_OF_STAFF = 'Magestore_Webpos::manage_order_created_at_location_of_staff';
    const PERMISSION_CREATED_AT_ALL_LOCATION = 'Magestore_Webpos::manage_order_created_at_all_location';
    const PERMISSION_CREATED_AT_OR_ASSIGNED_TO_LOCATION_OF_STAFF = 'Magestore_Webpos::manage_order_that_are_created_at_or_assigned_to_location_of_staff';
    const PERMISSION_ALL_ORDER_IN_SYSTEM = 'Magestore_Webpos::manage_all_orders_in_system';

    /**
     * @var \Magestore\Webpos\Api\Pos\PosRepositoryInterface
     */
    protected $posRepository;

    /**
     * @var \Magestore\Webpos\Api\Location\LocationRepositoryInterface
     */
    protected $locationRepository;

    /**
     * @var \Magestore\Webpos\Api\Data\Checkout\OrderInterfaceFactory
     */
    protected $posOrderInterfaceFactory;

    /**
     * Order constructor.
     * @param Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magestore\Webpos\Api\Pos\PosRepositoryInterface $posRepository
     * @param \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository
     * @param \Magestore\Webpos\Api\Data\Checkout\OrderInterfaceFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Webpos\Api\Pos\PosRepositoryInterface $posRepository,
        \Magestore\Webpos\Api\Location\LocationRepositoryInterface $locationRepository,
        \Magestore\Webpos\Api\Data\Checkout\OrderInterfaceFactory $posOrderInterfaceFactory
    )
    {
        parent::__construct($context, $objectManager, $storeManager);
        $this->posRepository = $posRepository;
        $this->locationRepository = $locationRepository;
        $this->posOrderInterfaceFactory = $posOrderInterfaceFactory;
    }


    /**
     * @param $order
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPosByOrder($order)
    {
        try {
            $info = [];
            $posId = $order->getPosId();
            $posModel = $this->posRepository->getById($posId);
            if ($posModel->getPosId()) {
                $info['pos_name'] = $posModel->getPosName();
                $locationId = $posModel->getLocationId();
                $locationModel = $this->locationRepository->getById($locationId);
                if ($locationModel->getLocationId()) {
                    $info['location_name'] = $locationModel->getName();
                }
            }
            return $info;
        } catch (\Exception $e) {
            return [
                'pos_name' => '',
                'location_name' => ''
            ];
        }
    }

    /**
     * @param $order
     * @return \Magento\Framework\Phrase
     */
    public function getPaymentStatus($order)
    {
        if ($order->getTotalPaid() < $order->getGrandTotal() && $order->getTotalPaid() > 0) {
            return __('Partial');
        } else {
            return __('Full');
        }
    }


    /**
     * @param array $permission
     * @param Collection $orderCollection
     * @param $locationId
     * @return Collection
     */
    public function applyPermissionForOrderCollect($permission, $orderCollection, $locationId)
    {
        if (
            in_array(self::PERMISSION_ALL, $permission) ||
            in_array(self::PERMISSION_ALL_ORDER_IN_SYSTEM, $permission)
        ) {
            return $orderCollection;
        }

        if (in_array(self::PERMISSION_CREATED_AT_OR_ASSIGNED_TO_LOCATION_OF_STAFF, $permission) && $locationId) {
            /** combine 2 + 3 */
            if (in_array(self::PERMISSION_CREATED_AT_ALL_LOCATION, $permission)) {

                if (!$this->isEnabledInventory()) {
                    $orderCollection
                        ->getSelect()
                        ->where('main_table.pos_location_id IS NOT NULL');
                    return $orderCollection;
                }

                $orderCollection
                    ->getSelect()
                    ->where('(main_table.pos_location_id = "' . $locationId . '") OR 
                     (main_table.warehouse_id = "' . $locationId . '") OR main_table.pos_location_id IS NOT NULL');
                return $orderCollection;
            }

            if (!$this->isEnabledInventory()) {
                return $orderCollection->addFieldToFilter('main_table.pos_location_id', $locationId);
            }

            $orderCollection
                ->getSelect()
                ->where('(main_table.pos_location_id = "' . $locationId . '") OR 
                     (main_table.warehouse_id = "' . $locationId . '")');
            return $orderCollection;
        }

        if (in_array(self::PERMISSION_CREATED_AT_ALL_LOCATION, $permission)) {
            $orderCollection
                ->getSelect()
                ->where('main_table.pos_location_id IS NOT NULL');
            return $orderCollection;
        }

        if (in_array(self::PERMISSION_CREATED_AT_LOCATION_OF_STAFF, $permission) && $locationId) {
            return $orderCollection->addFieldToFilter('main_table.pos_location_id', $locationId);
        }

        if (!$locationId) {
            $orderCollection
                ->getSelect()
                ->where('( main_table.entity_id = -1 ) 
            OR main_table.state = "' . \Magento\Sales\Model\Order::STATE_HOLDED . '"');
            return $orderCollection;
        }

        $orderCollection
            ->getSelect()
            ->where('( main_table.entity_id = -1 ) 
            OR (
                main_table.state = "' . \Magento\Sales\Model\Order::STATE_HOLDED . '" 
                AND 
                main_table.pos_location_id = ' . $locationId . '
            ) ');
        return $orderCollection;
    }

    /**
     * @param array $permission
     * @param Collection $orderCollection
     * @param $locationId
     * @return Collection
     */
    public function applyOutOfPermissionForOrderCollect($permission, $orderCollection, $locationId)
    {
        if (
            in_array(self::PERMISSION_ALL, $permission) ||
            in_array(self::PERMISSION_ALL_ORDER_IN_SYSTEM, $permission)
        ) {
            /** @var \Magestore\Webpos\Api\WebposManagementInterface $webposManagement */
            $webposManagement = $this->getObjectManager()->get('Magestore\Webpos\Api\WebposManagementInterface');

            if ($webposManagement->isMSIEnable() && $webposManagement->isWebposStandard()) {
                return $orderCollection;
            }

            $orderCollection
                ->getSelect()
                ->where('main_table.entity_id = -1');
            return $orderCollection;
        }


        if (in_array(self::PERMISSION_CREATED_AT_OR_ASSIGNED_TO_LOCATION_OF_STAFF, $permission) && $locationId) {
            /** combine 2 + 3 */
            if (in_array(self::PERMISSION_CREATED_AT_ALL_LOCATION, $permission)) {

                if (!$this->isEnabledInventory()) {
                    $orderCollection
                        ->getSelect()
                        ->where('main_table.pos_location_id IS NULL');

                    return $orderCollection;
                }

                $orderCollection
                    ->getSelect()
                    ->where('(main_table.pos_location_id IS NULL) AND 
                     (main_table.warehouse_id != "' . $locationId . '")');
                return $orderCollection;
            }

            if (!$this->isEnabledInventory()) {
                $orderCollection
                    ->getSelect()
                    ->where('
                main_table.pos_location_id IS NULL 
                OR 
                main_table.pos_location_id != ' . $locationId
                    );
                return $orderCollection;
            }


            $orderCollection
                ->getSelect()
                ->where(
                    '(main_table.pos_location_id IS NOT NULL AND main_table.pos_location_id != "' . $locationId . '")
                     OR
                     (main_table.pos_location_id IS NULL AND main_table.warehouse_id != "' . $locationId . '")'
                );
            return $orderCollection;
        }


        if (in_array(self::PERMISSION_CREATED_AT_ALL_LOCATION, $permission)) {
            $orderCollection
                ->getSelect()
                ->where('main_table.pos_location_id IS NULL');
            return $orderCollection;
        }

        if (in_array(self::PERMISSION_CREATED_AT_LOCATION_OF_STAFF, $permission) && $locationId) {

            $orderCollection
                ->getSelect()
                ->where('
                main_table.pos_location_id IS NULL 
                OR 
                main_table.pos_location_id != ' . $locationId
                );
            return $orderCollection;
        }


        $orderCollection
            ->getSelect()
            ->where('main_table.entity_id = -1');
        return $orderCollection;
    }

    /**
     * get correct status from order
     * @param \Magento\Sales\Model\Order $order
     * @return \Magestore\Webpos\Api\Data\Checkout\OrderInterface
     */
    public function verifyOrderReturn($order)
    {
        $posOrder = $this->posOrderInterfaceFactory->create();
        $posOrder->setData($order->getData());
        $posOrder->setAddresses($order->getAddresses());
        $posOrder->setPayments($order->getPayments());
        $posOrder->setItems($order->getItems());
        $posOrder->setStatusHistories($order->getStatusHistories());
        $this->_eventManager->dispatch('pos_verify_order_after', ['order' => $posOrder]);
        return $posOrder;
    }
}