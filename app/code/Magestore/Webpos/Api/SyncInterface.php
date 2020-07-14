<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api;

/**
 * @api
 */
interface SyncInterface
{
    /**
     * Queue name to check sync queue
     */
    const QUEUE_NAME = 'SYNC-QUEUE';
}
