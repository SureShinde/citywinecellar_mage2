<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Block\Supplier\Form;

use Magento\Customer\Model\AccountManagement;
use Magento\Customer\Model\Session;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ResetPassword extends \Magento\Framework\View\Element\Template
{

    /**
     * Get minimum password length
     *
     * @return string
     */
    public function getMinimumPasswordLength()
    {
        return $this->_scopeConfig->getValue(AccountManagement::XML_PATH_MINIMUM_PASSWORD_LENGTH);
    }

    /**
     * Get minimum password length
     *
     * @return string
     */
    public function getRequiredCharacterClassesNumber()
    {
        return $this->_scopeConfig->getValue(AccountManagement::XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER);
    }

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostUrl()
    {
        return $this->getUrl('dropship/supplier/createPasswordPost', ["_current" => true]);
    }
}
