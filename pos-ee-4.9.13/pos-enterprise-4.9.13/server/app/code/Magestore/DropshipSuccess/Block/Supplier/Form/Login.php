<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Block\Supplier\Form;

/**
 * Class Login
 * @package Magestore\DropshipSuccess\Block\Supplier\Form
 */
class Login extends \Magento\Framework\View\Element\Template
{
    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->getUrl('dropship/supplier/loginPost');
    }

    /**
     * Retrieve password forgotten url
     *
     * @return string
     */
    public function getForgotPasswordUrl()
    {
        return $this->getUrl('dropship/supplier/forgotPassword');
    }
}
