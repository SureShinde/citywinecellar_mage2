<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\PackRequest;

use Magestore\FulfilSuccess\Api\Data\PackRequestInterface;

class PackRequestDetail extends \Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail
{
    /**
     * @var \Magento\Shipping\Model\CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * @var \Magestore\FulfilSuccess\Service\PackRequest\PackRequestService
     */
    protected $packRequestService;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;
    
    /**
     * @var \Magestore\FulfilSuccess\Api\PackRequestRepositoryInterface 
     */
    protected $repository;

    /**
     * @var \Magestore\FulfilSuccess\Api\PermissionManagementInterface
     */
    protected $permissionManagement;

    /**
     * PackRequestDetail constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Sales\Helper\Reorder $reorderHelper
     * @param \Magento\Shipping\Model\CarrierFactory $carrierFactory
     * @param \Magestore\FulfilSuccess\Service\PackRequest\PackRequestService $packRequestService
     * @param \Magestore\FulfilSuccess\Api\PackRequestRepositoryInterface $repository
     * @param \Magestore\FulfilSuccess\Api\PermissionManagementInterface $permissionManagement
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Sales\Helper\Reorder $reorderHelper,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory,
        \Magestore\FulfilSuccess\Service\PackRequest\PackRequestService $packRequestService,
        \Magestore\FulfilSuccess\Api\PackRequestRepositoryInterface $repository,
        \Magestore\FulfilSuccess\Api\PermissionManagementInterface $permissionManagement,
        array $data = [])
    {
        $this->coreRegistry = $registry;
        $this->_carrierFactory = $carrierFactory;
        $this->packRequestService = $packRequestService;
        $this->repository = $repository;
        $this->permissionManagement = $permissionManagement;
        parent::__construct($context, $registry, $salesConfig, $reorderHelper, $data);
    }

    protected function _prepareLayout()
    {
        $this->_prepareChilds();
        parent::_prepareLayout();
        if ($this->getPackRequest()->getStatus() == PackRequestInterface::STATUS_CANCELED) {
            $this->setTemplate('Magestore_FulfilSuccess::packRequest/canceled.phtml');
        } else {
            $this->setTemplate('Magestore_FulfilSuccess::packRequest/pack_request_detail.phtml');
        }
    }

    /**
     * @return $this
     */
    public function _prepareChilds() {
        $shippingCarrier = $this->_carrierFactory->create(
            $this->getOrder()->getShippingMethod(true)->getCarrierCode()
        );
        
        $orderInfo = $this->getChildBlock('request_order');
        $requestAge = $this->packRequestService->getAge($this->getPackRequest());
        $orderInfo->setRequestAge($this->formatAge($requestAge));
        $orderInfo->setTitle(__('[Pack #%1]', $this->getPackRequestId()));        
        
        if (!$this->isPackedRequest()) {
            $moveNeedToShipUrl = $this->getUrl('fulfilsuccess/packRequest/moveNeedToShip', ['pack_request_id' => $this->getPackRequestId()]);
            

            if ($shippingCarrier && $shippingCarrier->isShippingLabelsAvailable()) {
                $this->addButton(
                    'pack_manually',
                    [
                        'label' => __('Pack'),
                        'onclick' => 'submitShipment(this);',
                        'class' => 'pack_manually primary'
                    ],
                    1,
                    10,
                    'bottom_right'
                );
            } else {
                $packManuallyUrl = $this->getUrl('fulfilsuccess/packRequest/savePackManually');

                $this->addButton(
                    'pack_manually',
                    [
                        'label' => __('Pack'),
                        'onclick' => 'submitAndReloadAreaPacking(\'pack_request_form\', \'' . $packManuallyUrl . '\');',
                        'class' => 'pack_manually primary'
                    ],
                    1,
                    10,
                    'bottom_right'
                );
            }            

            if ($this->permissionManagement->checkPermission('Magestore_FulfilSuccess::move_to_need_ship')) {
                $this->addButton(
                    'move_need_to_ship',
                    [
                        'label' => __('Move Items to Prepare Fulfil'),
                        'onclick' => 'moveNeedToShip(\'' . $moveNeedToShipUrl . '\');',
                        'class' => 'move_need_to_ship'
                    ],
                    1,
                    20,
                    'bottom_right'
                );
            }

            if ($this->permissionManagement->checkPermission('Magestore_FulfilSuccess::move_to_pick')) {
                $this->addButton(
                    'move_to_pick',
                    [
                        'label' => __('Move Items to Need-To-Pick'),
                        'class' => 'move_to_pick'
                    ],
                    1,
                    30,
                    'bottom_right'
                );
            }

        } else {
            $this->addButton(
                'print_items',
                [
                    'label' => __('Print Packed Items'),
                    'class' => 'print_items primary'
                ],
                1,
                10,
                'bottom_right'
            );
        }
    }

    /**
     * 
     * @return int
     */
    public function getWarehouseId()
    {
        return $this->getRequest()->getParam('warehouse_id');
    }
    
    /**
     * 
     * @return int
     */
    public function getPackRequestId()
    {
        return $this->getRequest()->getParam('pack_request_id');
    }
    
    /**
     * Get Pick Request
     * 
     * @return \Magestore\FulfilSuccess\Api\Data\PackRequestInterface
     */
    public function getPackRequest()
    {
        if(!$this->hasData('pack_request')) {
            $packRequest = $this->_coreRegistry->registry('current_pack_request');
            if(!$packRequest) {
                $packRequest = $this->repository->get($this->getPackRequestId());
            }
            $this->setData('pack_request', $packRequest);
        }
        return $this->getData('pack_request');
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
        return ($block && !$this->isPackedRequest()) ? $block : '';
    }

    /**
     * @return bool
     */
    public function isPackedRequest()
    {
        $packRequest = $this->coreRegistry->registry('current_pack_request');
        if ($packRequest && $packRequest->getId()) {
            if (in_array($packRequest->getData(PackRequestInterface::STATUS),
                [PackRequestInterface::STATUS_PACKED, PackRequestInterface::STATUS_CANCELED])) {
                return true;
            }
//            return ($packRequest->getData(PackRequestInterface::STATUS) == PackRequestInterface::STATUS_PACKED) ? true : false;
        }
        return false;
    }

    /**
     * 
     * @return string
     */
    public function getSubmitAndReloadAreaPackingUrl()
    {
        return $this->getUrl('fulfilsuccess/packRequest/savePackManually');
    }
}