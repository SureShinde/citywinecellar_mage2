<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml\Template;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magestore\BarcodeSuccess\Helper\Data;
use Magento\Framework\Controller\Result\JsonFactory;
use Magestore\BarcodeSuccess\Model\Locator\LocatorInterface;
use Magestore\BarcodeSuccess\Api\Data\BarcodeTemplateInterface;

/**
 * Class Preview
 * @package Magestore\BarcodeSuccess\Controller\Adminhtml\Template
 */
class Preview extends \Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractTemplate
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $defaultBarcode = $this->helper->generateExampleCode();
        $resultJson = $this->resultJsonFactory->create();
        $data = $this->getRequest()->getParam('data');
        $barcode = $this->getRequest()->getParam('barcode', $defaultBarcode);
        $qty = $this->getRequest()->getParam('qty');
        if(!empty($data)){
            $barcodeModel = $this->_objectManager->create('Magestore\BarcodeSuccess\Model\Barcode')->load($barcode, 'barcode');
            if ($barcodeModel->getId()){
                $productId = $barcodeModel->getData('product_id');
            } else {
                $productId = '';
            }
            $barcodes = [
                ['barcode' => $barcode, 'qty' => $qty, 'product_id' => $productId]
            ];
            $html = "";
            $block = $this->createBlock('Magestore\BarcodeSuccess\Block\Barcode\Container\Template','','Magestore_BarcodeSuccess::barcode/print/template.phtml');
            $block->setData('barcodes', $barcodes);
            if(isset($data['is_print_preview']) && isset($data['type'])){
                $template = $this->helper->getModel('Magestore\BarcodeSuccess\Api\Data\BarcodeTemplateInterface');
                $this->resource->load($template, $data['type']);
                $data = $template->getData();
            }
            $block->setData('template_data', $data);
            $html .= $block->toHtml();
            $resultJson->setData([
                'html' => $html,
                'success' => true
            ]);
        }else{
            $resultJson->setData([
                'messages' => __('Cannot find any data to preview'),
                'error' => true
            ]);
        }
        return $resultJson;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        $manage_barcode = $this->_authorization->isAllowed('Magestore_BarcodeSuccess::manage_barcode');
        $barcode_template = $this->_authorization->isAllowed('Magestore_BarcodeSuccess::barcode_template');
        return ($manage_barcode || $barcode_template);
    }
}
