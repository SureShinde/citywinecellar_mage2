<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magestore\BarcodeSuccess\Model\Locator\LocatorInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magestore\BarcodeSuccess\Helper\Data as Helper;
/**
 * Class View
 * @package Magestore\BarcodeSuccess\Controller\Adminhtml\Index
 */
class InlinePrintQty extends \Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractIndex
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * InlinePrintQty constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param LocatorInterface $locator
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        LocatorInterface $locator,
        Helper $helper
    ) {
        parent::__construct($context, $resultPageFactory, $helper, $locator);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $items = $this->getRequest()->getParam('items');
        if(empty($items)){
            $resultJson->setData([
                'messages' => __('Invalid editing data!'),
                'error' => true
            ]);
        }else{
            $savedItems = $this->locator->get('print_inline_edit_qty');
            if(!empty($savedItems)){
                $items = array_replace_recursive($savedItems, $items);
            }
            $this->locator->add('print_inline_edit_qty',$items);
            $resultJson->setData([
                'messages' => __('OK'),
                'success' => true
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
        return $this->_authorization->isAllowed('Magestore_BarcodeSuccess::print_barcode');
    }
}
