<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Service\Locator;


class UserService implements UserServiceInterface
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;
    
    public function __construct(
        \Magento\Backend\Model\Auth\Session $authSession
    )
    {
        $this->authSession = $authSession;
    }
    
    /**
     * Get currently User Id
     * 
     * @return int|null
     */
    public function getCurrentUserId()
    {
        if($this->authSession->getUser()) {
            return $this->authSession->getUser()->getId();
        }
        return null;
    }
    
}