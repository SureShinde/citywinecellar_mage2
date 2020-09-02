<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposHeartlandTerminal\Api;

/**
 * Interface TerminalServiceInterface
 *
 * @package Magestore\WebposHeartlandTerminal\Api
 */
interface TerminalServiceInterface
{
    /**
     * Define constrains
     */
    const CODE = 'heartlandterminal_integration';
    const TITLE = 'Heartland Terminal';
    const ONLINE_CODE = 'heartland';
    const CONFIG_PATH = 'webpos/payment/heartlandterminal';
    /**
     * API save connected reader
     *
     * @param  \Magestore\WebposHeartlandTerminal\Api\Data\ConnectedReaderInterface $request
     *
     * @return \Magestore\WebposHeartlandTerminal\Api\Data\ConnectedReaderInterface
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveConnectedReader($request);
}
