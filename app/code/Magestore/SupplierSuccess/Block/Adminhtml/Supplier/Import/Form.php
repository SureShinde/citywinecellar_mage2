<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Block\Adminhtml\Supplier\Import;

/**
 * Supplier Form import
 */
class Form extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;
    /**
     * Form constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->urlBuilder = $context->getUrlBuilder();
        $this->setUseContainer(true);
    }

    /**
     * Get csv sample link
     *
     * @return mixed
     */
    public function getCsvSampleLink()
    {
        $url = $this->getUrl(
            'suppliersuccess/supplier/downloadsample',
            [
                '_secure' => true,
                'id' => $this->getRequest()->getParam('id')
            ]
        );
        return $url;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return __('Please choose a CSV file to import products for the supplier.'
            . ' You can download this sample CSV file');
    }

    /**
     * Get import urk
     *
     * @return mixed
     */
    public function getImportLink()
    {
        return $this->getUrl(
            'suppliersuccess/supplier/import',
            [
                '_secure' => true,
                'id' => $this->getRequest()->getParam('id')
            ]
        );
    }

    /**
     * Get import title
     *
     * @return string
     */
    public function getTitle()
    {
        return __('Import products');
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
