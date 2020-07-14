<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Request\Actions;

/**
 * Class RefundAction
 *
 * @package Magestore\Webpos\Model\Request\Actions
 */
class RefundAction extends AbstractAction
{
    const ACTION_TYPE = "refund";

    /**
     * @inheritDoc
     */
    public function prepareParams($data, $params)
    {
        $data['order_increment_id'] = $params['creditmemo']['order_increment_id'];
        $data['request_increment_id'] = $params['creditmemo']['increment_id'];
        return $data;
    }
}
