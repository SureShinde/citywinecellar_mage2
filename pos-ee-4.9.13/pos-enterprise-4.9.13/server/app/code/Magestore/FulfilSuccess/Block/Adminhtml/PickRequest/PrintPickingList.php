<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\PickRequest;

use Magestore\FulfilSuccess\Service\PickRequest\BarcodeOrderService;

class PrintPickingList extends \Magento\Backend\Block\Template
{
    /**
     * @var BarcodeOrderService
     */
    protected $barcodeOrderService;
    
    /**
     * @var \Magento\Backend\Model\Auth\Session 
     */
    protected $authSession;

    /**
     * @var
     */
    protected $helper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        BarcodeOrderService $barcodeOrderService,    
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magestore\FulfilSuccess\Helper\Data $helper,
        array $data = []
    )
    {
        $this->barcodeOrderService = $barcodeOrderService;
        $this->authSession = $authSession;
        $this->helper = $helper;

        parent::__construct($context, $data);
    }    

    /**
     * get current date time
     * 
     * @return string
     */
    public function getCurrentDateTime()
    {
        return $this->formatDate(null, \IntlDateFormatter::MEDIUM, true);
    }
    
    /**
     * Get barcode source
     * 
     * @param string $data
     * @return string
     */
    public function getPickBarcodeSource($data)
    {
        return $this->barcodeOrderService->getBarcodeSource($data, BarcodeOrderService::BARCODE_TYPE_PICK);
    }

    /**
     * Get barcode source
     *
     * @param string $data
     * @return string
     */
    public function getPackBarcodeSource($data)
    {
        return $this->barcodeOrderService->getBarcodeSource($data, BarcodeOrderService::BARCODE_TYPE_PACK);
    }
    
    /**
     * get username of current user
     * 
     * @return string
     */
    public function getCurrentUser()
    {
        return $this->authSession->getUser()->getName();
    }

    /**
     *
     */
    public function getBarcodeSetupGuideUrl(){
        return $this->helper->getSetupGuideUrl();
    }
}
