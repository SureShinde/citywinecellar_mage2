<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\PurchaseOrderSuccess\Ui\Component\Listing\Columns\PurchaseOrder;
/**
 * Class Actions
 * @package Magestore\PurchaseOrderSuccess\Ui\Component\Listing\Columns\PurchaseOrder
 */
class Actions extends \Magento\Catalog\Ui\Component\Listing\Columns\ProductActions
{
    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'purchaseordersuccess/purchaseOrder/view',
                        ['id' => $item['purchase_order_id']]
                    ),
                    'label' => __('View'),
                    'hidden' => false,
                ];
            }
        }

        return $dataSource;
    }
}
