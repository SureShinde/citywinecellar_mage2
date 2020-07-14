<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Block\Supplier\Form;

/**
 * Class ForgotPassword
 * @package Magestore\DropshipSuccess\Block\Supplier\Form
 */
class ForgotPassword extends \Magento\Framework\View\Element\Template
{
    /**
     * Get login URL
     *
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->getUrl('dropship/supplier/login');
    }

    /**
     * get post url
     *
     * @return string
     */
    public function getPostUrl()
    {
        return $this->getUrl('dropship/supplier/forgotPasswordPost');
    }
}
