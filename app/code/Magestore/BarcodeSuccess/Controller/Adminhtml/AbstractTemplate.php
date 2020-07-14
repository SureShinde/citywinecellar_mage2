<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magestore\BarcodeSuccess\Helper\Data;
use Magestore\BarcodeSuccess\Model\Locator\LocatorInterface;
use Magestore\BarcodeSuccess\Model\ResourceModel\Template as TemplateResource;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class AbstractIndex
 * @package Magestore\BarcodeSuccess\Controller\Adminhtml
 */
abstract class AbstractTemplate extends \Magento\Backend\App\Action
{
    
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_BarcodeSuccess::barcode_template';    
    
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Magestore\BarcodeSuccess\Helper\Data
     */
    protected $helper;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var TemplateResource
     */
    protected $resource;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * AbstractTemplate constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $data
     * @param LocatorInterface $locator
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $data,
        LocatorInterface $locator,
        TemplateResource $resource,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $data;
        $this->locator = $locator;
        $this->resource = $resource;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_BarcodeSuccess::barcode_template');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Barcode Label Templates'));
        return $resultPage;
    }

    /**
     *
     * @param string $class
     * @param string $name
     * @param string $template
     * @return block type
     */
    public function createBlock($class,$name = '',$template = ""){
        $block = "";
        try{
            $block = $this->_view->getLayout()->createBlock($class,$name);
            if($block && $template != ""){
                $block->setTemplate($template);
            }
        }catch(\Exception $e){
            return $block;
        }
        return $block;
    }

}
