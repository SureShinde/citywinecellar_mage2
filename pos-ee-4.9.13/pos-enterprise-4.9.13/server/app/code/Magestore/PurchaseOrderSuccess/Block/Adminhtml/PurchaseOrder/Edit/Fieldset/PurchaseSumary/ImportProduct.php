<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset\PurchaseSumary;

/***
 * Class ImportProduct
 * @package Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Fieldset\PurchaseSumary
 */
class ImportProduct extends  \Magento\Backend\Block\Widget\Form\Generic {

    protected $_template = 'Magestore_PurchaseOrderSuccess::purchaseorder/form/import.phtml';

    /**
     * @var int
     */
    protected $purchaseId;

    /**
     * @var int
     */
    protected $supplierId;

    /**
     * ImportProduct constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->setUseContainer(true);
    }


    /**
     * Get html id
     *
     * @return mixed
     */
    public function getHtmlId()
    {
        if (null === $this->getData('id')) {
            $this->setData('id', $this->mathRandom->getUniqueHash('id_'));
        }
        return $this->getData('id');
    }

    /**
     * Get current purchase id
     * 
     * @return int|mixed
     */
    public function getPurchaseId(){
        if(!$this->purchaseId)
            $this->purchaseId = $this->getRequest()->getParam('purchase_id');
        return $this->purchaseId;
    }

    /**
     * Get current supplier id
     * 
     * @return int|mixed
     */
    public function getSupplierId(){
        if(!$this->supplierId)
            $this->supplierId = $this->getRequest()->getParam('supplier_id');
        return $this->supplierId;
    }

    /**
     * Get sample download csv file url
     * 
     * @return string
     */
    public function getCsvSampleLink() {
        return $this->getUrl(
            'purchaseordersuccess/purchaseOrder_product/downloadSample', 
            array(
                'purchase_id' => $this->getPurchaseId(),
                'supplier_id' => $this->getSupplierId()
            )
        );
    }

    /**
     * Get import url
     * 
     * @return string
     */
    public function getImportUrl(){
        return $this->getUrl(
            'purchaseordersuccess/purchaseOrder_product/import', 
            array(
                'purchase_id' => $this->getPurchaseId(),
                'supplier_id' => $this->getSupplierId(),
                'type' => $this->getRequest()->getParam('type')
            )
        );
    }

}