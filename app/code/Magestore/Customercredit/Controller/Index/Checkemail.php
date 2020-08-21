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

namespace Magestore\Customercredit\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * Class Checkemail
 *
 * Check email controller
 */
class Checkemail extends \Magento\Framework\App\Action\Action implements HttpGetActionInterface
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * Checkemail constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->_customerFactory = $customerFactory;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = [];
        $email = $this->getRequest()->getParam('email');
        $existed = $this->_customerFactory->create()->getCollection()->addFieldToFilter('email', $email)->getSize();
        if ($existed) {
            $result['existed'] = 1;
        }
        return $this->getResponse()->setBody(\Zend_Json::encode($result));
    }
}
