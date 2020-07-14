<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Magestore\SupplierSuccess\Block\Adminhtml\Supplier\Import\PricingList;

/**
 * Class Form
 * @package Magestore\SupplierSuccess\Block\Adminhtml\Supplier\Import
 */
class Form extends  \Magestore\SupplierSuccess\Block\Adminhtml\Supplier\Import\Form
{

    /**
     * Get csv sample link
     *
     * @return mixed
     */
    public function getCsvSampleLink() {
        $url = $this->getUrl('suppliersuccess/supplier_pricinglist/downloadsample',
                array(
                    '_secure' => true,
                    'id' => $this->getRequest()->getParam('id')
                ));
        return $url;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent() {
        return __('Please choose a CSV file to import pricelist supplier. You can download this sample CSV file');
    }

    /**
     * Get import urk
     *
     * @return mixed
     */
    public function getImportLink() {
        return $this->getUrl('suppliersuccess/supplier_pricinglist/import',
                array(
                    '_secure' => true,
                    'id' => $this->getRequest()->getParam('id')
                ));
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
}