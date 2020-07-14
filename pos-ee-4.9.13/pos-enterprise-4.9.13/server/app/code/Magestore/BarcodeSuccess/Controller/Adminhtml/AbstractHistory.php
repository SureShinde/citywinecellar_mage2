<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magestore\BarcodeSuccess\Model\Locator\LocatorInterface;

/**
 * Class AbstractHistory
 * @package Magestore\BarcodeSuccess\Controller\Adminhtml
 */
abstract class AbstractHistory extends \Magento\Backend\App\Action
{
    
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magestore_BarcodeSuccess::history';    
    
    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * AbstractIndex constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        LocatorInterface $locator
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->locator = $locator;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_BarcodeSuccess::history');
        $resultPage->getConfig()->getTitle()->prepend(__('Barcode Created History'));
        return $resultPage;
    }

}
