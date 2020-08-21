<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Customercredit
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

namespace Magestore\Customercredit\Controller\Adminhtml\Creditproduct;

use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * Class NewAction
 *
 * Credit product new action controller
 */
class NewAction extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Customercredit::creditproduct');
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $set = $this->_objectManager->create(\Magento\Catalog\Model\Product::class)->getDefaultAttributeSetId();
        $this->_session->setCreditProductCreate(true);
        return $this->_redirect('catalog/product/new', ['type' => 'customercredit', 'set' => $set]);
    }
}
