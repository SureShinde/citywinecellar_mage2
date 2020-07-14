<?php
namespace Magestore\Rewardpoints\Model\Plugin;
/**
 * Class UpdateDiscountForOrder
 * @package Magestore\Rewardpoints\Model\Plugin
 */
class UpdateDiscountForOrder
{

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var \Magento\Checkout\Model\SessionFactory
     */
    protected $_checkoutSessionFactory;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;
    /**
     *
     */
    const AMOUNT_Payment = 'payment_fee';
    /**
     *
     */
    const AMOUNT_SUBTOTAL = 'subtotal';

    /**
     * UpdateDiscountForOrder constructor.
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Checkout\Model\SessionFactory $checkoutSessionFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Quote\Model\Quote $quote,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Checkout\Model\SessionFactory $checkoutSessionFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->quote = $quote;
        $this->logger = $logger;
        $this->_checkoutSessionFactory = $checkoutSessionFactory;
        $this->_registry = $registry;
    }

    /**
     * @param $cart
     * @param $result
     * @return mixed
     */
    public function afterGetAmounts($cart, $result)
    {
        $total = $result;
        $quote = $this->_checkoutSessionFactory->create()->getQuote();
        $paymentMethod = $quote->getPayment()->getMethod();
        if(!$paymentMethod){
            $paymentMethod = 'paypal_express';
        }
        $paypalMehodList = ['payflowpro','payflow_link','payflow_advanced','braintree_paypal','paypal_express_bml','payflow_express_bml','payflow_express','paypal_express'];

        if(in_array($paymentMethod,$paypalMehodList)){
            $total[self::AMOUNT_SUBTOTAL] = $total[self::AMOUNT_SUBTOTAL] - $quote->getRewardpointsDiscount();

        }

        return  $total;
    }

    /**
     * @param $cart
     */
    public function beforeGetAllItems($cart)
    {
        $quote = $this->_checkoutSessionFactory->create()->getQuote();
        $paymentMethod = $quote->getPayment()->getMethod();
        if(!$paymentMethod){
            $paymentMethod = 'paypal_express';
        }
        $paypalMehodList = ['payflowpro','payflow_link','payflow_advanced','braintree_paypal','paypal_express_bml','payflow_express_bml','payflow_express','paypal_express'];
        if($quote->getRewardpointsDiscount() && !$quote->getRewardpointsTotal() && in_array($paymentMethod,$paypalMehodList)){
            if(method_exists($cart , 'addCustomItem' ))
            {
                $cart->addCustomItem(__("Reward Point Discount"), 1 ,  -1.00 * $quote->getRewardpointsDiscount());
                $quote->setRewardpointsTotal(true);
            }
        }
    }
}