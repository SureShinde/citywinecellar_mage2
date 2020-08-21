<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposAuthorizenet\Model;

/**
 * Webpos Authorizenet Service
 */
class AuthorizenetService implements \Magestore\WebposAuthorizenet\Api\AuthorizenetServiceInterface
{
    /**
     * @var \Magestore\WebposAuthorizenet\Api\AuthorizenetInterface
     */
    protected $authorizenet;

    /**
     * AuthorizenetService constructor.
     * @param \Magestore\WebposAuthorizenet\Api\AuthorizenetInterface $authorizenet
     */
    public function __construct(
        \Magestore\WebposAuthorizenet\Api\AuthorizenetInterface $authorizenet
    ) {
        $this->authorizenet = $authorizenet;
    }

    /**
     * @inheritDoc
     */
    public function isEnable()
    {
        $hasSDK = $this->authorizenet->validateRequiredSDK();
        $configs = $this->authorizenet->getConfig();
        return ($hasSDK
            && $configs['enable']
            && !empty($configs['transaction_key'])
            && !empty($configs['api_login'])
        ) ? true : false;
    }

    /**
     * @inheritDoc
     */
    public function getConfigurationError()
    {
        $message = '';
        $hasSDK = $this->authorizenet->validateRequiredSDK();
        $configs = $this->authorizenet->getConfig();
        if (!$hasSDK) {
            $message = __(
                'Authorizenet SDK not found, please go to the configuration to get the instruction to install the SDK'
            );
        } else {
            if ($configs['enable']) {
                if (empty($configs['transaction_key']) || empty($configs['api_login'])) {
                    $message = __('Authorizenet application client id and client secret are required');
                }
            } else {
                $message = __('Authorizenet integration is disabled');
            }
        }
        return $message;
    }

    /**
     * @inheritDoc
     */
    public function finishPayment($token, $amount)
    {
        error_reporting(E_ALL & ~E_DEPRECATED);
        $result = $this->authorizenet->completePayment($token, $amount);
        error_reporting(E_ALL);
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function canConnectToApi()
    {
        error_reporting(E_ALL & ~E_DEPRECATED);
        $result = $this->authorizenet->canConnectToApi();
        error_reporting(E_ALL);
        return $result;
    }
}
