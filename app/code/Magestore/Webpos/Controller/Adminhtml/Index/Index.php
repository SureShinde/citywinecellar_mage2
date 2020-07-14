<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Index;
/**
 * Class Index
 * @package Magestore\Webpos\Controller\Adminhtml\Index
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /**
         * @var \Magento\Store\Model\StoreManagerInterface $storeManager
         */
        $helperData = $objectManager->get('\Magestore\Webpos\Helper\Data');
        $url = $helperData->getPosUrl();
        if (strpos(strtolower($this->getRequest()->getServer('SERVER_SOFTWARE')), 'nginx') !== FALSE) {
            $url = $url . '/index.html';
        }
        $this->_redirect($url);
    }
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Webpos::checkout');
    }
}