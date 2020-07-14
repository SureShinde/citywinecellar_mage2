<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail;

/**
 * Class Scan
 * @package Magestore\FulfilSuccess\Block\Adminhtml\AbstractRequest\Detail
 */
class Scan extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @var string
     */
    protected $placeholder = '';

    /**
     * @var array
     */
    protected $sourceData = [];

    /**
     * Scan constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
        $this->initData();
    }

    /**
     * Prepare layout
     */
    protected function _prepareLayout()
    {
        $this->setTemplate('Magestore_FulfilSuccess::abstractRequest/detail/scan.phtml');
        parent::_prepareLayout();
    }

    /**
     * Get title of scan section
     * @return string
     */
    public function getTitle(){
        return $this->title;
    }

    /**
     * @return string
     */
    public function getScanUrl(){
        return $this->url;
    }

    /**
     * @return string
     */
    public function getPlaceHolder(){
        return $this->placeholder;
    }

    /**
     * Init data
     */
    public function initData(){

    }

    /**
     * Get source for offline scaning
     * @return JSON string
     */
    public function getDataSource(){
        return \Zend_Json::encode($this->sourceData);
    }

    /**
     * Get Javascript service
     *
     * @return string
     */
    public function getJsService()
    {
        return "";
    }

}