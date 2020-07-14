<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Checkout;

/**
 * Class PosOrder
 *
 * @package Magestore\Webpos\Model\Checkout
 */
class PosOrder extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_PENDING = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_FAILED = 4;

    const PARAM_ORDER_POS_ID = 'new_order_pos_id';
    const PARAM_ORDER_LOCATION_ID = 'new_order_location_id';

    /**
     * @var \Magestore\Webpos\Log\Logger
     */
    protected $logger;
    /**
     * @var \Magestore\Webpos\Helper\Data
     */
    protected $helper;

    /**
     * PosOrder constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\Webpos\Model\ResourceModel\Checkout\PosOrder $resource
     * @param \Magestore\Webpos\Model\ResourceModel\Checkout\PosOrder\Collection $resourceCollection
     * @param \Magestore\Webpos\Log\Logger $logger
     * @param \Magestore\Webpos\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\Webpos\Model\ResourceModel\Checkout\PosOrder $resource,
        \Magestore\Webpos\Model\ResourceModel\Checkout\PosOrder\Collection $resourceCollection,
        \Magestore\Webpos\Log\Logger $logger,
        \Magestore\Webpos\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->logger = $logger;
        $this->helper = $helper;
    }

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init(\Magestore\Webpos\Model\ResourceModel\Checkout\PosOrder::class);
    }

    /**
     * Save request place order
     *
     * @param \Magento\Framework\Webapi\Rest\Request $request
     */
    public function saveRequest(\Magento\Framework\Webapi\Rest\Request $request)
    {
        $params = $request->getBodyParams();

        $existedRequest = clone $this;
        $existedRequest->load($params['order']['increment_id'], 'increment_id');
        if ($existedRequest->getId()) {
            return;
        }

        $data = [
            'status' => self::STATUS_PENDING,
            'increment_id' => $params['order']['increment_id'],
            'session_id' => $request->getParam(
                \Magestore\WebposIntegration\Controller\Rest\RequestProcessor::SESSION_PARAM_KEY
            ),
            'store_id' => $this->helper->getCurrentStoreView()->getId(),
            'location_id' => $params['order']['pos_location_id'],
            'pos_staff_id' => $params['order']['pos_staff_id'],
            'pos_id' => $params['order']['pos_id'],
            'order_created_time' => $params['order']['created_at'],
            'params' => json_encode($params),
        ];

        $this->setData($data);

        try {
            $this->save();
        } catch (\Exception $e) {
            $this->logger->info($e->getTraceAsString());
        }
    }
}
