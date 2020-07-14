<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\PickRequest;

use Magestore\FulfilSuccess\Api\Data\PickRequestInterface;
use Magestore\FulfilSuccess\Api\PermissionManagementInterface;

class PickRequestDetail extends \Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail
{
    /**
     * @var \Magento\Shipping\Model\CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * @var \Magestore\FulfilSuccess\Service\PickRequest\PickRequestService
     */
    protected $pickRequestService;

    /**
     * @var \Magestore\FulfilSuccess\Service\Location\LocationServiceInterface
     */
    protected $locationService;

    /**
     * @var \Magestore\FulfilSuccess\Api\PickRequestRepositoryInterface
     */
    protected $repository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var PermissionManagementInterface
     */
    protected $permissionManagement;

    /**
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Sales\Helper\Reorder $reorderHelper
     * @param \Magento\Shipping\Model\CarrierFactory $carrierFactory
     * @param \Magestore\FulfilSuccess\Service\PickRequest\PickRequestService $pickRequestService
     * @param \Magestore\FulfilSuccess\Service\Location\LocationServiceInterface $locationService
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Sales\Helper\Reorder $reorderHelper,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory,
        \Magestore\FulfilSuccess\Service\PickRequest\PickRequestService $pickRequestService,
        \Magestore\FulfilSuccess\Service\Location\LocationServiceInterface $locationService,
        \Magestore\FulfilSuccess\Api\PickRequestRepositoryInterface $repository,
        PermissionManagementInterface $permissionManagement,
        array $data = [])
    {
        $this->coreRegistry = $registry;
        $this->_carrierFactory = $carrierFactory;
        $this->pickRequestService = $pickRequestService;
        $this->locationService = $locationService;
        $this->repository = $repository;
        $this->permissionManagement = $permissionManagement;
        parent::__construct($context, $registry, $salesConfig, $reorderHelper, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if($this->getPickRequest()->getStatus() == PickRequestInterface::STATUS_CANCELED) {
            $this->setTemplate('Magestore_FulfilSuccess::pickRequest/canceled.phtml');
        } else {
            $this->setTemplate('Magestore_FulfilSuccess::pickRequest/detail.phtml');
        }
    }

    /**
     * @return $this
     */
    protected function _prepareChilds()
    {
        $this->addTopChild($this->getOrderInfoBlock(), 'request_order');
        $this->addTopChild($this->getAccountBlock(), 'request_account');

        $orderInfo = $this->getChildBlock('request_order');
        $requestAge = $this->pickRequestService->getAge($this->getPickRequest());
        $orderInfo->setRequestAge($this->formatAge($requestAge));
        $orderInfo->setTitle(__('[Pick #%1]', $this->getPickRequestId()));

        if(!$this->isPickedRequest()){
            $this->addButton(
                'mark_picked',
                [
                    'label' => __('Mark as Picked'),
                    'class' => 'mark_picked primary'
                ],
                1,
                10,
                'bottom_right'
            );

            $this->addButton(
                'mark_picked_all',
                [
                    'label' => __('Mark All Items Picked'),
                    'class' => 'mark_picked_all'
                ],
                1,
                20,
                'bottom_right'
            );

            if($this->permissionManagement->checkPermission(PermissionManagementInterface::PICK_MOVE_TO_NEED_SHIP)) {
                $this->addButton(
                    'move_to_need_ship',
                    [
                        'label' => __('Move Items to Prepare Fulfil'),
                        'class' => 'move_to_need_ship'
                    ],
                    1,
                    30,
                    'bottom_right'
                );
            }
        }else{
            $this->addButton(
                'print_items',
                [
                    'label' => __('Print Picked Items'),
                    'class' => 'print_items primary'
                ],
                1,
                10,
                'bottom_right'
            );
        }
    }


    /**
     * Get items html
     *
     * @return string
     */
    public function getItemsHtml()
    {
        if($this->getPickRequest()->getStatus() == PickRequestInterface::STATUS_CANCELED) {
            $itemsBlock = $this->getChildBlock('request_items');
            $itemsBlock->setTemplate('Magestore_FulfilSuccess::pickRequest/detail/canceled_items.phtml');
            $itemRender = $itemsBlock->getChildBlock('default');
            $itemRender->setTemplate('Magestore_FulfilSuccess::pickRequest/detail/items/renderer/canceled_default.phtml');
        }
        return $this->getChildHtml('request_items');
    }

    /**
     *
     * @return int|null
     */
    public function getWarehouseId()
    {
        return $this->getPickRequest()->getWarehouseId();
        //return $this->locationService->getCurrentWarehouseId();
    }

    /**
     *
     * @return int|null
     */
    public function getPickRequestId()
    {
        return $this->getRequest()->getParam('pick_request_id');
    }

    /**
     * Get Pick Request
     *
     * @return \Magestore\FulfilSuccess\Api\Data\PickRequestInterface
     */
    public function getPickRequest()
    {
        if(!$this->hasData('pick_request')) {
            $pickRequest = $this->_coreRegistry->registry('current_pick_request');
            if(!$pickRequest) {
                $pickRequest = $this->repository->getById($this->getRequest()->getParam('pick_request_id'));
            }
            $this->setData('pick_request', $pickRequest);
        }
        return $this->getData('pick_request');
    }

    /**
     * Checks the possibility of creating shipping label by current carrier
     *
     * @return bool
     */
    public function canCreateShippingLabel()
    {
        $shippingCarrier = $this->_carrierFactory->create(
            $this->getOrder()->getShippingMethod(true)->getCarrierCode()
        );
        return $shippingCarrier && $shippingCarrier->isShippingLabelsAvailable();
    }

    /**
     * Get before items html
     *
     * @return string
     */
    public function getBeforeItemsHtml()
    {
        $block = $this->getChildHtml('before_items');
        return ($block && !$this->isPickedRequest())?$block:'';
    }

    /**
     * @return bool
     */
    public function isPickedRequest(){
        $pickRequest = $this->coreRegistry->registry('current_pick_request');
        if($pickRequest && $pickRequest->getId()){
            return ($pickRequest->getData(PickRequestInterface::STATUS) == PickRequestInterface::STATUS_PICKED)?true:false;
        }
        return false;
    }
}
