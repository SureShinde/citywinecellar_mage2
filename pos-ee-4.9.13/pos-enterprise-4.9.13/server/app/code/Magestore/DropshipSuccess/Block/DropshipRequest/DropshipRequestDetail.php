<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Block\DropshipRequest;

use Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface;
use Magestore\DropshipSuccess\Api\DropshipRequestRepositoryInterface;

/**
 * Class DropshipRequestDetail
 * @package Magestore\DropshipSuccess\Block\DropshipRequest
 */
class DropshipRequestDetail extends \Magento\Framework\View\Element\Template
{

    /**
     * @var DropshipRequestRepositoryInterface
     */
    protected $dropshipRequestRepository;

    /**
     * @var \Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface
     */
    protected $dropshipRequest;

    /**
     * DropshipRequestDetail constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param DropshipRequestRepositoryInterface $dropshipRequestRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        DropshipRequestRepositoryInterface $dropshipRequestRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dropshipRequestRepository = $dropshipRequestRepository;
        $this->dropshipRequest = $this->getDropshipRequest();
    }

    /**
     *
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('Magestore_DropshipSuccess::dropshiprequest/dropship_request_detail.phtml');
    }

    /**
     * Get before items html
     *
     * @return string
     */
    public function getBeforeItemsHtml()
    {

    }

    /**
     * Get items html
     *
     * @return string
     */
    public function getCustomerInfo()
    {
        return $this->getChildHtml('supplier.dropship.details');
    }

    /**
     * Get items html
     *
     * @return string
     */
    public function getItemsHtml()
    {
        return $this->getChildHtml('dropship_items');
    }

    /**
     * Get tracking html
     *
     * @return string
     */
    public function getTrackingHtml()
    {
        return $this->getChildHtml('shipment_tracking');
    }

    /**
     * Get button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        if (!in_array($this->dropshipRequest->getStatus(),
            [DropshipRequestInterface::STATUS_SHIPPED, DropshipRequestInterface::STATUS_CANCELED])
        ) {
            return $this->getChildHtml('button_html');
        }
    }

    /**
     * get submit url (create shipment)
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('dropship/dropshipRequest/createShipment');
    }

    /**
     * @return \Magestore\DropshipSuccess\Api\Data\DropshipRequestInterface
     */
    public function getDropshipRequest()
    {
        $dropshipRequestId = $this->getRequest()->getParam('dropship_id');
        return $this->dropshipRequestRepository->getById($dropshipRequestId);
    }

    /**
     * get dropship request id
     * @return int|null
     */
    public function getDropshipRequestId()
    {
        return $this->dropshipRequest->getId();
    }

    /**
     * get supplier id
     * @return int|null
     */
    public function getSupplierId()
    {
        return $this->dropshipRequest->getSupplierId();
    }

    /**
     * get order id
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->dropshipRequest->getOrderId();
    }
}