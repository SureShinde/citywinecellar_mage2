<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Storepickup
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Storepickup\Helper;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Email
 * @package Magestore\Storepickup\Helper
 */
class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;
    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;
    /**
     * @var Renderer
     */
    protected $addressRenderer;
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;
    /**
     * @var Order\Email\Container\OrderIdentity
     */
    protected $identityContainer;
    /**
     * @var \Magestore\Storepickup\Model\StoreFactory
     */
    protected $storeFactory;

    /**
     *
     */
    const TEMPLATE_ID_NONE_EMAIL = 'none_email';

    /**
     *
     */
    const XML_PATH_ADMIN_EMAIL_IDENTITY = "trans_email/ident_general";
    /**
     *
     */
    const XML_PATH_ADMIN_EMAIL_GENERAL_NAME = 'trans_email/ident_general/name';
    /**
     *
     */
    const XML_PATH_SALES_EMAIL_IDENTITY = "trans_email/ident_sales";
    /**
     *
     */
    const XML_PATH_SALES_EMAIL_IDENTITY_NAME = "trans_email/ident_sales/name";

    /**
     *
     */
    const XML_PATH_NEW_ORDER_TO_ADMIN_EMAIL = 'storepickup/email_configuration/shopadmin_email_template';
    /**
     *
     */
    const XML_PATH_NEW_ORDER_TO_STORE_OWNER_EMAIL = 'storepickup/email_configuration/storeowner_email_template';
    /**
     *
     */
    const XML_PATH_STATUS_ORDER_TO_STORE_OWNER_EMAIL = 'storepickup/email_configuration/storeowner_email_change_status';
    /**
     *
     */
    const XML_PATH_ADMIN_EMAIL = 'storepickup/email_configuration/shopadmin_email';

    /**
     *
     */
    const TYPE_SEND_NEW_ORDER_TO_ADMIN = 'admin';
    /**
     *
     */
    const TYPE_SEND_NEW_ORDER_TO_STORE_OWNER = 'new_to_store_owner';
    /**
     *
     */
    const TYPE_CHANGE_ORDER_STATUS_TO_STORE_OWNER = 'change_status';

    /**
     * Email constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param Renderer $addressRenderer
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param Order\Email\Container\OrderIdentity $identityContainer
     * @param \Magestore\Storepickup\Model\StoreFactory $storeFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        Renderer $addressRenderer,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Sales\Model\Order\Email\Container\OrderIdentity $identityContainer,
        \Magestore\Storepickup\Model\StoreFactory $storeFactory
    )
    {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->paymentHelper = $paymentHelper;
        $this->addressRenderer = $addressRenderer;
        $this->inlineTranslation = $inlineTranslation;
        $this->backendHelper = $backendHelper;
        $this->identityContainer = $identityContainer;
        $this->storeFactory = $storeFactory;
    }

    /**
     * @return mixed
     */
    public function getEmailList(){
        return $this->scopeConfig->getValue('trans_email');
    }

    /**
     * Returns payment info block as HTML.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return string
     */
    private function getPaymentHtml(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        return $this->paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $this->identityContainer->getStore()->getStoreId()
        );
    }

    /**
     * @param Order $order
     * @return string|null
     */
    public function getFormattedShippingAddress($order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }

    /**
     * @param Order $order
     * @return string|null
     */
    public function getFormattedBillingAddress($order)
    {
        return $this->addressRenderer->format($order->getBillingAddress(), 'html');
    }

    /**
     * @param $order
     * @param $type
     * @return $this
     */
    public function sendEmail($order, $type){

        $storeId = $order->getStore()->getId();

        $adminEmail = $this->scopeConfig->getValue(self::XML_PATH_ADMIN_EMAIL, ScopeInterface::SCOPE_STORE, $storeId);
        $this->inlineTranslation->suspend();
        if ($type == self::TYPE_SEND_NEW_ORDER_TO_ADMIN){
            $template = $this->scopeConfig->getValue(self::XML_PATH_NEW_ORDER_TO_ADMIN_EMAIL, ScopeInterface::SCOPE_STORE, $storeId);
            $sendTo = array(
                $this->scopeConfig->getValue(
                    "trans_email/".$adminEmail,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $storeId
                )
            );
        } else{
            $storeLocation = $this->getStorePickupByOrder($order);

            if (!$storeLocation) {
                return $this;
            }


            $sendTo = array(
                array(
                    'name' => $storeLocation->getData('owner_name'),
                    'email' => $storeLocation->getData('owner_email'),
                ),
            );
            $order->setStoreOwnerName($storeLocation->getData('owner_name'));

            if (!$storeLocation->getData('owner_email')){
                return $this;
            }

            if ($type == self::TYPE_SEND_NEW_ORDER_TO_STORE_OWNER){
                $template = $this->scopeConfig->getValue(self::XML_PATH_NEW_ORDER_TO_STORE_OWNER_EMAIL, ScopeInterface::SCOPE_STORE, $storeId);
            } else {
                $template = $this->scopeConfig->getValue(self::XML_PATH_STATUS_ORDER_TO_STORE_OWNER_EMAIL, ScopeInterface::SCOPE_STORE, $storeId);
            }
        }


        if ($template === self::TEMPLATE_ID_NONE_EMAIL) {
            return $this;
        }
        foreach ($sendTo as $recipient) {
            try {
                $order->setAdminName($this->scopeConfig->getValue(
                    'trans_email/'.$adminEmail.'/name',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $storeId
                ));

                $adminUrl = $this->backendHelper->getHomePageUrl();
                $transport = $this->transportBuilder->setTemplateIdentifier(
                    $template
                )->setTemplateOptions(
                    ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId]
                )->setTemplateVars(
                    [
                        'adminUrl' => $adminUrl,
                        'order' => $order,
                        'billing' => $order->getBillingAddress(),
                        'payment_html' => $this->getPaymentHtml($order, $storeId),
                        'store' => $order->getStore(),
                        'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
                        'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
                        'indentSupportEmail' => $this->scopeConfig->getValue('trans_email/ident_support/email', ScopeInterface::SCOPE_STORE, $storeId),
                        'storePhone' => $this->scopeConfig->getValue('general/store_information/phone', ScopeInterface::SCOPE_STORE, $storeId),
                        'storeHour' => $this->scopeConfig->getValue('general/store_information/hours', ScopeInterface::SCOPE_STORE, $storeId),
                    ]
                )->setFrom(
                    $this->scopeConfig->getValue(
                        self::XML_PATH_SALES_EMAIL_IDENTITY,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $storeId
                    )
                )->addTo(
                    $recipient['email'],
                    $recipient['name']
                )->getTransport();
                $transport->sendMessage();
            } catch (\Magento\Framework\Exception\MailException $ex) {
                $this->inlineTranslation->resume();
                $this->_logger->error($ex->getMessage());
            } catch (\Exception $e) {
                $this->_logger->error($e->getMessage());
            }
        }
        $this->inlineTranslation->resume();

        return $this;
    }

    /**
     * @param $order
     * @return null
     */
    public function getStorePickupByOrder($order)
    {
        $pickupId = $order->getData('storepickup_id');
        if ($pickupId){
            $storePickup = $this->storeFactory->create()->load($pickupId);
            return $storePickup;
        } else {
            return null;
        }
    }
}
