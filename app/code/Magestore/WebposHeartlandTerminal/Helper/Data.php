<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposHeartlandTerminal\Helper;

use Magestore\WebposIntegration\Controller\Rest\RequestProcessor;
use Magestore\WebposHeartlandTerminal\Api\TerminalServiceInterface;

/**
 * Class Data
 *
 * @package Magestore\WebposHeartlandTerminal\Helper
 */
class Data extends \Magestore\Webpos\Helper\Data
{
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
        $configPath = TerminalServiceInterface::CONFIG_PATH;
        if (!empty($node)) {
            return "{$configPath}/{$node}";
        }
        return $configPath;
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
