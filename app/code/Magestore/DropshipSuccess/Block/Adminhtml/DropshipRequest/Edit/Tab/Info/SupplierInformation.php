<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Block\Adminhtml\DropshipRequest\Edit\Tab\Info;
use Magestore\DropshipSuccess\Block\Adminhtml\DropshipRequest\Edit\Tab\AbstractDropshipRequestTab;

/**
 * Class SupplierInformation
 * @package Magestore\DropshipSuccess\Block\Adminhtml\DropshipRequest\Edit\Tab\Info
 */
class SupplierInformation extends AbstractDropshipRequestTab
{
    /**
     * @return \Magestore\SupplierSuccess\Api\Data\SupplierInterface|null
     */
    public function getSupplier()
    {
        /** @var \Magestore\DropshipSuccess\Model\DropshipRequest $dropshipRequest */
        $dropshipRequest = $this->getDropshipRequest();
        $supplierId = $dropshipRequest->getSupplierId();
        if ($supplierId) {
            try {
                $supplier = $this->supplierRepository->getById($supplierId);
                return $supplier;
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * @return \Magestore\SupplierSuccess\Api\Data\SupplierInterface|null
     */
    public function getDropshipRequestStatus()
    {
        $dropshipRequest = $this->getDropshipRequest();
        $status = $dropshipRequest->getStatus();
        $statusList = $this->dropshipRequestInterface->getStatusOption();
        if (isset($statusList[$status])) {
            return $statusList[$status];
        }
        return $statusList[0];
    }
}
