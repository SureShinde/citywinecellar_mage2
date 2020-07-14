<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposStripeTerminal\Helper;

use \Magestore\WebposStripeTerminal\Api\StripeTerminalServiceInterface;
use Magestore\WebposIntegration\Controller\Rest\RequestProcessor;

/**
 * Class Data
 *
 * @package Magestore\WebposStripeTerminal\Helper
 */
class Data extends \Magestore\WebposStripe\Helper\Data
{
    /**
     *  Check install SDK
     *
     * @return bool
     */
    public function validateRequiredSDK()
    {
        return class_exists("\\Stripe\\Stripe");
    }

    /**
     * Get config
     *
     * @param array $excludes
     * @return array
     */
    public function getConfig($excludes = [])
    {
        $configData = [];
        $configItems = [
            'title',
            'enable',
            'sort_order',
        ];

        foreach ($excludes as $exclude) {
            if (empty($configItems[$exclude])) {
                continue;
            }

            unset($configItems[$exclude]);
        }

        foreach ($configItems as $configItem) {
            $configData[$configItem] = $this->getStoreConfig($this->getConfigPath($configItem));
        }

        return $configData;
    }

    /**
     * Get config path
     *
     * @param string $node
     * @return string
     */
    public function getConfigPath($node = '')
    {
        $configPath = StripeTerminalServiceInterface::CONFIG_PATH;
        if (!empty($node)) {
            return "{$configPath}/{$node}";
        }
        return $configPath;
    }

    /**
     * Get secret key
     *
     * @return string|null
     */
    public function getSecretKey()
    {
        if ($this->isSandboxMode()) {
            return $this->getStoreConfig($this->getConfigPath('sandbox_secret_key'));
        }
        return $this->getStoreConfig($this->getConfigPath('secret_key'));
    }

    /**
     * Check is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getStoreConfig($this->getConfigPath('enable')) == 1;
    }

    /**
     * Check is sandbox mode
     *
     * @return bool
     */
    public function isSandboxMode()
    {
        return $this->getStoreConfig($this->getConfigPath('is_sandbox')) == 1;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return StripeTerminalServiceInterface::CODE;
    }

    /**
     * Get object manager
     *
     * @return \Magento\Framework\App\ObjectManager
     */
    public function getObjectManager()
    {
        return $this->_objectManager;
    }

    /**
     * Get Current Session
     *
     * @return \Magestore\Webpos\Api\Data\Staff\SessionInterface
     */
    public function getCurrentSession()
    {
        try {
            $sessionId = $this->_request->getParam(RequestProcessor::SESSION_PARAM_KEY);
            /** @var \Magestore\Webpos\Api\Staff\SessionRepositoryInterface $sessionRepository */
            $sessionRepository = $this->getObjectManager()->get(
                \Magestore\Webpos\Api\Staff\SessionRepositoryInterface::class
            );
            return $sessionRepository->getBySessionId($sessionId);
        } catch (\Exception $exception) {
            return null;
        }
    }
}
