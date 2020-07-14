<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Service;

use Magestore\SupplierSuccess\Model\Supplier;

class EmailService
{
    const XML_PATH_ADMIN_EMAIL_IDENTITY      = "trans_email/ident_general";
    const TEMPLATE_ID_NONE_EMAIL    = '0';
    const NEW_PASSWORD_TO_SUPPLIER    = 'suppliersuccess/dropship/send_password_to_supplier';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * send new password to supplier
     * @param Supplier $supplier
     * @param $newPassword
     */
    public function sendNewPasswordTosupplier(Supplier $supplier, $newPassword)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $templateId = $this->scopeConfig->getValue(self::NEW_PASSWORD_TO_SUPPLIER, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($templateId === self::TEMPLATE_ID_NONE_EMAIL) {
            return;
        }
        $this->inlineTranslation->suspend();
        if($supplier->getId()) {
            $emailContact = $supplier->getContactEmail();
            $nameContact = $supplier->getContactName();
            try {
                $transport = $this->transportBuilder->setTemplateIdentifier(
                    $templateId
                )->setTemplateOptions(
                    ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId]
                )->setTemplateVars(
                    [
                        'supplier' => $supplier,
                        'newpassword' => $newPassword
                    ]
                )->setFrom(
                    $this->scopeConfig->getValue(
                        self::XML_PATH_ADMIN_EMAIL_IDENTITY,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $storeId
                    )
                )->addTo(
                    $emailContact,
                    $nameContact
                )->getTransport();
                $transport->sendMessage();
            } catch (\Magento\Framework\Exception\MailException $ex) {
            }
            $this->inlineTranslation->resume();
        }
    }
}