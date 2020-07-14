<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Block\Adminhtml\PurchaseOrder\Edit\Button;

use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Status;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\Type;

/**
 * Class Save
 */
class Back extends \Magestore\PurchaseOrderSuccess\Block\Adminhtml\Button\AbstractButton
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $purchaseOrder = $this->registry->registry('current_purchase_order');
        $request = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Framework\App\RequestInterface');

        $type = $purchaseOrder->getType();

        if(!$purchaseOrder->getId()) {
            $type = $request->getParam('type', Type::TYPE_PURCHASE_ORDER);
        }

        $url = '';
        if($type == Type::TYPE_QUOTATION) {
            $url = $this->getUrl('purchaseordersuccess/quotation/index', []);
        } elseif ($type == Type::TYPE_PURCHASE_ORDER) {
            $url = $this->getUrl('purchaseordersuccess/purchaseOrder/index', []);
        }

        return [
            'label' => __('Back'),
            'class' => 'back',
            'on_click' => sprintf("setLocation('%s')", $url)
        ];
    }
}
