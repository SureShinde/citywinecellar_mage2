<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Controller\Adminhtml\AdjustStock;


class GetBarcodeJson extends \Magestore\AdjustStock\Controller\Adminhtml\AdjustStock\AdjustStock
{
    public function execute()
    {
        $this->getResponse()->representJson(
            $this->adjustStockManagement->getSelectBarcodeProductListJson(
                $this->_request->getParam('adjuststock_id')
            )
        );
    }

}


