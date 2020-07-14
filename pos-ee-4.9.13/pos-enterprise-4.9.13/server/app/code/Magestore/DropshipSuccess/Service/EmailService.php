<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Service;

use Magento\Sales\Model\Order\Shipment;
use Magestore\DropshipSuccess\Api\Data\SupplierPricelistUploadInterface;
use Magestore\DropshipSuccess\Model\DropshipRequest;
use Magestore\SupplierSuccess\Api\Data\SupplierInterface;

/**
 * Class EmailService
 * @package Magestore\DropshipSuccess\Service
 */
class EmailService
{
    const XML_PATH_SALES_EMAIL_IDENTITY      = "trans_email/ident_sales";
    const XML_PATH_ADMIN_EMAIL_IDENTITY      = "trans_email/ident_general";
    const XML_PATH_SALES_EMAIL_IDENTITY_NAME = "trans_email/ident_sales/name";
    const DROPSHIP_SUBMIT_REQUEST_TO_SUPPLIER = "dropshipsuccess/general/submit_request_to_supplier";
    const DROPSHIP_CANCEL_REQUEST_TO_SUPPLIER = "dropshipsuccess/general/cancel_request_to_supplier";
    const TEMPLATE_ID_NONE_EMAIL    = '0';
    const DROPSHIP_SHIPMENT_TO_ADMIN = 'dropshipsuccess/general/confirm_shipment_to_admin';
    const XML_PATH_ADMIN_EMAIL_IDENTITY_EMAIL = 'trans_email/ident_general/email';
    const CANCEL_DROPSHIP_TO_ADMIN = 'dropshipsuccess/general/cancel_dropship_to_admin';
    const FORGOT_PASSWORD_TO_SUPPLIER = 'dropshipsuccess/general/forgot_password_to_supplier';
    const DROPSHIP_PRICELIST_TO_ADMIN = 'dropshipsuccess/general/send_pricelist_to_admin';
    /**
     * @var \Magestore\DropshipSuccess\Helper\Data
     */
    protected $helper;

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

    /**
     * @var SupplierRepositoryInterface
     */
    protected $supplierRepository;

    /**
     * @var PricelistUploadService
     */
    protected $pricelistUploadService;
    /**
     * @var
     */
    protected $url;

    /**
     * EmailService constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magestore\DropshipSuccess\Helper\Data $helper
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface $supplierRepository
     * @param PricelistUploadService $pricelistUploadService
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magestore\DropshipSuccess\Helper\Data $helper,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface $supplierRepository,
        \Magento\Framework\UrlInterface $url,
        PricelistUploadService $pricelistUploadService
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->supplierRepository = $supplierRepository;
        $this->pricelistUploadService = $pricelistUploadService;
        $this->url = $url;
    }

    /**
     * Send submit dropship email to supplier
     *
     * @param $requestId
     * @param $supplierId
     */
    public function sendSubmitDropshipEmailToSupplier($request, $requestUrl)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $templateId = $this->helper->getStoreConfig(self::DROPSHIP_SUBMIT_REQUEST_TO_SUPPLIER);
        if ($templateId === self::TEMPLATE_ID_NONE_EMAIL) {
            return;
        }
        $this->inlineTranslation->suspend();
        $supplierId = $request->getSupplierId();
        $supplier = $this->supplierRepository->getById($supplierId);
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
                        'url' => $requestUrl,
                        'id' => $request->getId()
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

    /**
     * Send submit dropship email to supplier
     *
     * @param $requestId
     * @param $supplierId
     */
    public function sendCancelDropshipEmailToSupplier($request, $requestUrl)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $templateId = $this->helper->getStoreConfig(self::DROPSHIP_CANCEL_REQUEST_TO_SUPPLIER);
        if ($templateId === self::TEMPLATE_ID_NONE_EMAIL) {
            return;
        }
        $this->inlineTranslation->suspend();
        $supplierId = $request->getSupplierId();
        $supplier = $this->supplierRepository->getById($supplierId);
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
                        'url' => $requestUrl,
                        'id' => $request->getId()
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

    /**
     * send email inform to admin after create shipment
     * @param Shipment $shipment
     * @param DropshipRequest $dropshipRequest
     */
    public function sendDropshipShipmentToAdmin(Shipment $shipment, DropshipRequest $dropshipRequest)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $templateId = $this->helper->getStoreConfig(self::DROPSHIP_SHIPMENT_TO_ADMIN);
        if ($templateId === self::TEMPLATE_ID_NONE_EMAIL) {
            return;
        }
        $this->inlineTranslation->suspend();
        $order = $shipment->getOrder();
        $supplierId = $dropshipRequest->getSupplierId();
        $supplier = $this->supplierRepository->getById($supplierId);
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
                        'shipment' => $shipment,
                        'order' => $order,
                        'supplier' => $supplier,
                        'dropshiprequest' => $dropshipRequest
                    ]
                )->setFrom(
                    [
                        'name' => $nameContact,
                        'email' => $emailContact
                    ]
                )->addTo(
                    $this->scopeConfig->getValue(
                        self::XML_PATH_ADMIN_EMAIL_IDENTITY_EMAIL,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $storeId
                    )
                )->getTransport();
                $transport->sendMessage();
            } catch (\Magento\Framework\Exception\MailException $ex) {
            }
            $this->inlineTranslation->resume();
        }
    }

    /**
     * send email inform to admin after cancel dropship
     * @param DropshipRequest $dropshipRequest
     */
    public function sendCancelDropshipToAdmin(DropshipRequest $dropshipRequest)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $templateId = $this->helper->getStoreConfig(self::CANCEL_DROPSHIP_TO_ADMIN);
        if ($templateId === self::TEMPLATE_ID_NONE_EMAIL) {
            return;
        }
        $this->inlineTranslation->suspend();
        $supplierId = $dropshipRequest->getSupplierId();
        $supplier = $this->supplierRepository->getById($supplierId);
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
                        'dropshiprequest' => $dropshipRequest
                    ]
                )->setFrom(
                    [
                        'name' => $nameContact,
                        'email' => $emailContact
                    ]
                )->addTo(
                    $this->scopeConfig->getValue(
                        self::XML_PATH_ADMIN_EMAIL_IDENTITY_EMAIL,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $storeId
                    )
                )->getTransport();
                $transport->sendMessage();
            } catch (\Magento\Framework\Exception\MailException $ex) {
            }
            $this->inlineTranslation->resume();
        }
    }

    /**
     * send email forgot password to supplier
     * @param SupplierInterface $supplier
     * @param $forgotPasswordUrl
     */
    public function sendEmailForgotEmailToSupplier(SupplierInterface $supplier, $forgotPasswordUrl)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $templateId = $this->helper->getStoreConfig(self::FORGOT_PASSWORD_TO_SUPPLIER);
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
                        'url' => $forgotPasswordUrl
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

    /**
     * send email pricelist to admin
     * @param SupplierPricelistUploadInterface $pricelistUpload
     */
    public function sendEmailPricelistToAdmin(SupplierPricelistUploadInterface $pricelistUpload)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $templateId = $this->helper->getStoreConfig(self::DROPSHIP_PRICELIST_TO_ADMIN);
        if ($templateId === self::TEMPLATE_ID_NONE_EMAIL) {
            return;
        }
        $this->inlineTranslation->suspend();
        $supplierId = $pricelistUpload->getSupplierId();
        $supplier = $this->supplierRepository->getById($supplierId);
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
                        'url' => $this->url->getUrl('dropship/supplier/downloadPriceList', ['supplier_id' => $pricelistUpload->getSupplierId(),
                            'filename' => $pricelistUpload->getTitle(), 'file_upload' => $pricelistUpload->getFileUpload()])
                    ]
                )->setFrom(
                    [
                        'name' => $nameContact,
                        'email' => $emailContact
                    ]
                )->addTo(
                    $this->scopeConfig->getValue(
                        self::XML_PATH_ADMIN_EMAIL_IDENTITY_EMAIL,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $storeId
                    )
                )->getTransport();
                $transport->sendMessage();
            } catch (\Magento\Framework\Exception\MailException $ex) {
            }
            $this->inlineTranslation->resume();
        }
    }
}