<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Ui\DataProvider;

/**
 * Class Context
 * @package Magestore\OrderSuccess\Ui\DataProvider
 */
class Context
{
    /**
     * @var \Magestore\OrderSuccess\Model\ResourceModel\Order\NeedVerify\CollectionFactory
     */
    protected $needVerifyCollectionFatory;

    /**
     * @var \Magestore\OrderSuccess\Model\ResourceModel\Order\NeedShip\CollectionFactory
     */
    protected $needShipCollectionFatory;

    /**
     * @var \Magestore\OrderSuccess\Model\ResourceModel\Order\AwaitingPayment\CollectionFactory
     */
    protected $awaitingPaymentCollectionFatory;

    /**
     * @var \Magestore\OrderSuccess\Model\ResourceModel\Order\BackOrder\CollectionFactory
     */
    protected $backorderCollectionFatory;

    /**
     * @var \Magestore\OrderSuccess\Model\ResourceModel\Order\Canceled\CollectionFactory
     */
    protected $canceledCollectionFatory;

    /**
     * @var \Magestore\OrderSuccess\Model\ResourceModel\Order\Completed\CollectionFactory
     */
    protected $completedCollectionFatory;

    /**
     * @var \Magestore\OrderSuccess\Model\ResourceModel\Order\Hold\CollectionFactory
     */
    protected $holdCollectionFatory;

    /**
     * @var \Magestore\OrderSuccess\Model\ResourceModel\Order\AllOrder\CollectionFactory
     */
    protected $allOrderCollectionFatory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Context constructor.
     * @param \Magestore\OrderSuccess\Model\ResourceModel\Order\NeedVerify\CollectionFactory $needVerifyCollectionFatory
     * @param \Magestore\OrderSuccess\Model\ResourceModel\Order\NeedShip\CollectionFactory $needShipCollectionFatory
     * @param \Magestore\OrderSuccess\Model\ResourceModel\Order\AwaitingPayment\CollectionFactory $awaitingPaymentCollectionFatory
     * @param \Magestore\OrderSuccess\Model\ResourceModel\Order\BackOrder\CollectionFactory $backorderCollectionFatory
     * @param \Magestore\OrderSuccess\Model\ResourceModel\Order\Canceled\CollectionFactory $canceledCollectionFatory
     * @param \Magestore\OrderSuccess\Model\ResourceModel\Order\Completed\CollectionFactory $completedCollectionFatory
     * @param \Magestore\OrderSuccess\Model\ResourceModel\Order\Hold\CollectionFactory $holdCollectionFatory
     * @param \Magestore\OrderSuccess\Model\ResourceModel\Order\AllOrder\CollectionFactory $pendingFulfillmentCollectionFatory
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magestore\OrderSuccess\Model\ResourceModel\Order\NeedVerify\CollectionFactory $needVerifyCollectionFatory,
        \Magestore\OrderSuccess\Model\ResourceModel\Order\NeedShip\CollectionFactory $needShipCollectionFatory,
        \Magestore\OrderSuccess\Model\ResourceModel\Order\AwaitingPayment\CollectionFactory $awaitingPaymentCollectionFatory,
        \Magestore\OrderSuccess\Model\ResourceModel\Order\BackOrder\CollectionFactory $backorderCollectionFatory,
        \Magestore\OrderSuccess\Model\ResourceModel\Order\Canceled\CollectionFactory $canceledCollectionFatory,
        \Magestore\OrderSuccess\Model\ResourceModel\Order\Completed\CollectionFactory $completedCollectionFatory,
        \Magestore\OrderSuccess\Model\ResourceModel\Order\Hold\CollectionFactory $holdCollectionFatory,
        \Magestore\OrderSuccess\Model\ResourceModel\Order\AllOrder\CollectionFactory $allOrderCollectionFatory,
        \Magento\Framework\App\RequestInterface $request

    ) {
        $this->needVerifyCollectionFatory = $needVerifyCollectionFatory;
        $this->needShipCollectionFatory = $needShipCollectionFatory;
        $this->awaitingPaymentCollectionFatory = $awaitingPaymentCollectionFatory;
        $this->backorderCollectionFatory = $backorderCollectionFatory;
        $this->canceledCollectionFatory = $canceledCollectionFatory;
        $this->completedCollectionFatory = $completedCollectionFatory;
        $this->holdCollectionFatory = $holdCollectionFatory;
        $this->allOrderCollectionFatory = $allOrderCollectionFatory;
        $this->request = $request;
    }

    /**
     * @return \Magestore\OrderSuccess\Model\ResourceModel\Order\NeedVerify\CollectionFactory
     */
    public function getNeedVerifyCollectionFactory()
    {
        return $this->needVerifyCollectionFatory;
    }

    /**
     * @return \Magestore\OrderSuccess\Model\ResourceModel\Order\NeedShip\CollectionFactory
     */
    public function getNeedShipCollectionFactory()
    {
        return $this->needShipCollectionFatory;
    }

    /**
     * @return \Magestore\OrderSuccess\Model\ResourceModel\Order\AwaitingPayment\CollectionFactory
     */
    public function getAwaitingPaymentCollectionFactory()
    {
        return $this->awaitingPaymentCollectionFatory;
    }

    /**
     * @return \Magestore\OrderSuccess\Model\ResourceModel\Order\BackOrder\CollectionFactory
     */
    public function getBackOrderCollectionFactory()
    {
        return $this->backorderCollectionFatory;
    }

    /**
     * @return \Magestore\OrderSuccess\Model\ResourceModel\Order\Canceled\CollectionFactory
     */
    public function getCanceledCollectionFactory()
    {
        return $this->canceledCollectionFatory;
    }

    /**
     * @return \Magestore\OrderSuccess\Model\ResourceModel\Order\Completed\CollectionFactory
     */
    public function getCompletedCollectionFactory()
    {
        return $this->completedCollectionFatory;
    }

    /**
     * @return \Magestore\OrderSuccess\Model\ResourceModel\Order\Hold\CollectionFactory
     */
    public function getHoldCollectionFactory()
    {
        return $this->holdCollectionFatory;
    }

    /**
     * @return \Magestore\OrderSuccess\Model\ResourceModel\Order\AllOrder\CollectionFactory
     */
    public function getAllOrderCollectionFactory()
    {
        return $this->allOrderCollectionFatory;
    }

    /**
     * @return \Magento\Framework\App\RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }


}