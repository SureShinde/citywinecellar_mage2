<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Request\Actions;

/**
 * Class CancelAction
 *
 * @package Magestore\Webpos\Model\Request\Actions
 */
class CancelAction extends AbstractAction
{
    const ACTION_TYPE = "cancel";

    /**
     * @inheritDoc
     */
    public function prepareParams($data, $params)
    {
        $data['order_increment_id'] = $params['increment_id'];
        $data['request_increment_id'] = $params['request_increment_id'];
        return $data;
    }
}
