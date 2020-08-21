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

use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * Class Sendemail
 *
 * Send email controller
 */
class Sendemail extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface
{
    /**
     * @var \Magestore\Customercredit\Helper\Account
     */
    protected $_accountHelper;
    /**
     * @var \Magestore\Customercredit\Model\CustomercreditFactory
     */
    protected $_customercreditFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customersession;

    /**
     * Sendemail constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magestore\Customercredit\Helper\Account $accountHelper
     * @param \Magestore\Customercredit\Model\CustomercreditFactory $customercreditFactory
     * @param \Magento\Customer\Model\Session $customersesion
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magestore\Customercredit\Helper\Account $accountHelper,
        \Magestore\Customercredit\Model\CustomercreditFactory $customercreditFactory,
        \Magento\Customer\Model\Session $customersesion
    ) {
        $this->_accountHelper = $accountHelper;
        $this->_customercreditFactory = $customercreditFactory;
        $this->_customersession = $customersesion;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if (!$this->_accountHelper->isLoggedIn()) {
            return $this->_redirect('customer/account/login');
        }
        $this->_customersession->setData("sentemail", 'yes');
        $this->_customersession->setData("is_credit_code", 'yes');
        $email = $this->getRequest()->getParam('email');
        $value = $this->getRequest()->getParam('value');
        $message = $this->getRequest()->getParam('message');
        $ran_num = rand(1, 1000000);
        $keycode = sha1(sha1(sha1($ran_num)));
        $this->_customersession->setData("emailcode", $keycode);
        $this->_customercreditFactory->create()->sendVerifyEmail($email, $value, $message, $keycode);
        $result = [];
        $result['success'] = 1;
        return $this->getResponse()->setBody(\Zend_Json::encode($result));
    }
}
