<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magestore\BarcodeSuccess\Helper\Data;
use Magento\Framework\Controller\Result\JsonFactory;
use Magestore\BarcodeSuccess\Model\Locator\LocatorInterface;

/**
 * Class GetTemplateOption
 * @package Magestore\BarcodeSuccess\Controller\Adminhtml\Index
 */
class GetTemplateOption extends \Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractIndex
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * PrintBarcode constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param Data $data
     * @param LocatorInterface $locator
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        Data $data,
        LocatorInterface $locator
    ) {
        parent::__construct($context, $resultPageFactory, $data, $locator);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $html = "";
        $defaultTemplate = $this->helper->getStoreConfig('barcodesuccess/general/default_barcode_template');
        $options = $this->helper->getModel('Magestore\BarcodeSuccess\Model\Source\Template')->toOptionArray();
        if(count($options) > 0){
            foreach ($options as $option){
                $isDefault = (isset($defaultTemplate) && $defaultTemplate == $option['value'])?true:false;
                $html .= "<option value='".$option['value']."'";
                if($isDefault){
                    $html .= " selected ";
                }
                $html .= ">";
                $html .= $option['label'];
                $html .= "</option>";
            }
        }
        $resultJson->setData([
            'html' => $html,
            'success' => true
        ]);
        return $resultJson;
    }
}
