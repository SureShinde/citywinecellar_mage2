<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Service\DropshipRequest;


/**
 * Class DataProcessService
 * @package Magestore\DropshipSuccess\Service\DropshipRequest
 */
class DataProcessService
{
    /**
     * Define data-code
     */
    const DATA_CODE = 'dropship';

    /**
     * Define data keys
     */
    const ORDER_ID = 'order_id';
    /**
     *
     */
    const REQUESTS = 'requests';

    /**
     *
     * @param array $data
     * @return array\bool
     */
    public function processPostedRequestData($data)
    {
        $requestData = [];
        if (empty($data['order_id'])) {
            return false;
        }
        if (!isset($data['packages']) || !count($data['packages'])) {
            return false;
        }
        $requestData['order_id'] = $data['order_id'];
        $requestData['requests'] = [];
        foreach ($data['packages'] as $package) {
            if (!isset($package['params']['container']) || $package['params']['container'] != self::DATA_CODE) {
                continue;
            }
            if (!isset($package['items']) || !count($package['items'])) {
                continue;
            }
            foreach($package['items'] as $item) {
                if(!isset( $item['resource'])
                        || !isset($item['qty'])
                        || !isset($item['order_item_id'])) {
                    continue;
                }
                $supplierId = $item['resource'];
                $itemId = $item['order_item_id'];
                if(!isset($requestData['requests'][$supplierId][$itemId])) {
                    $requestData['requests'][$supplierId][$itemId] = floatval($item['qty']);
                } else {
                    $requestData['requests'][$supplierId][$itemId] += floatval($item['qty']);
                }
            }
        }

        return $requestData;
    }

}
