<?php

namespace Magestore\Webpos\Model\Checkout\Order\Payment;

/**
 * Class Error
 *
 * @package Magestore\Webpos\Model\Checkout\Order\Payment
 */
class Error extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'webpos_order_payment_error';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Magestore\Webpos\Model\ResourceModel\Sales\Order\Payment\Error::class);
    }

    /**
     * Save order payment error
     *
     * @param \Magestore\Webpos\Model\Checkout\Order\Payment $paymentModel
     * @throws \Exception
     */
    public function saveErrorLog(\Magestore\Webpos\Model\Checkout\Order\Payment $paymentModel)
    {
        $data = [
            'order_id' => $paymentModel->getOrderId(),
            'params' => json_encode($paymentModel->getData())
        ];

        $this->unsetData('id');
        $this->setData($data);
        $this->save();
    }
}
