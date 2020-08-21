<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Index;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * Giftvoucher Index History Action
 *
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class History extends \Magestore\Giftvoucher\Controller\Action implements HttpGetActionInterface
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        if (!$this->customerLoggedIn()) {
            $resultRedirectFactory = $this->getRedirectFactory()->setPath(
                'customer/account/login',
                ['_secure' => true]
            );
            return $resultRedirectFactory;
        }
        $resultPageFactory = $this->getPageFactory();
        $resultPageFactory->getConfig()->getTitle()->set('Gift Card History');
        /** @var \Magento\Framework\View\Element\Html\Links $navigationBlock */
        $navigationBlock = $resultPageFactory->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('giftvoucher');
        }
        return $resultPageFactory;
    }
}
