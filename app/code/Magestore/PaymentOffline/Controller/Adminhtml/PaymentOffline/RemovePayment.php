<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PaymentOffline\Controller\Adminhtml\PaymentOffline;
use Magento\Backend\App\Action;


class RemovePayment extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magestore\PaymentOffline\Service\PaymentOfflineService
     */
    protected $paymentOfflineService;

    /**
     * RemovePayment constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magestore\PaymentOffline\Service\PaymentOfflineService $paymentOfflineService
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magestore\PaymentOffline\Service\PaymentOfflineService $paymentOfflineService

    )
    {
        $this->request = $request;
        $this->paymentOfflineService = $paymentOfflineService;
        parent::__construct($context);
    }

    /**
     * RemovePayment action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $paymentCode = $this->request->getParam('payment_code');
        if ($paymentCode) {
            $this->paymentOfflineService->removePayment($paymentCode);
        }
    }
}