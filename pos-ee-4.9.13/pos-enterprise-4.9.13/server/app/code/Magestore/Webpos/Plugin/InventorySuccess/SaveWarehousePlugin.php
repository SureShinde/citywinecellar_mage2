<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Plugin\InventorySuccess;


class SaveWarehousePlugin
{
    /**
     * @var \Magestore\Webpos\Helper\Product\CustomSale $customSaleHelper
     */
    protected $customSaleHelper;

    /**
     * SaveWarehousePlugin constructor.
     * @param \Magestore\Webpos\Helper\Product\CustomSale $customSaleHelper
     */
    public function __construct(
        \Magestore\Webpos\Helper\Product\CustomSale $customSaleHelper
    )
    {
        $this->customSaleHelper = $customSaleHelper;
    }

    /**
     * @param \Magestore\InventorySuccess\Controller\Adminhtml\Warehouse\Save $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute($subject, $result)
    {
        try {
            $params = $subject->getRequest()->getParams();
            $id = isset($params['warehouse_id']) && $params['warehouse_id'] > 0 ? $params['warehouse_id'] : null;
            $warehouse = \Magento\Framework\App\ObjectManager::getInstance()
                ->create('Magestore\InventorySuccess\Model\Warehouse');

            // if is new
            if (!$warehouse->checkWarehouseCode($id)) {
                $this->customSaleHelper->updateProduct();
            }

            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}