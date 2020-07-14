<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Service\PurchaseOrderCode;

class PurchaseOrderCodeService
{
    const DEFAULT_ID = 1;
    const CODE_LENGTH = 8;
    /**
     * @var \Magestore\PurchaseOrderSuccess\Model\PurchaseOrderCodeFactory
     */
    protected $purchaseOrderCodeFactory;

    /**
     * PurchaseOrderCodeService constructor.
     * @param \Magestore\PurchaseOrderSuccess\Model\PurchaseOrderCodeFactory $purchaseOrderCodeFactory
     */
    public function __construct(
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrderCodeFactory $purchaseOrderCodeFactory
    ){
        $this->purchaseOrderCodeFactory = $purchaseOrderCodeFactory;
    }

    /**
     * Generate purchase order code
     *
     * @param string $prefix
     * @return string $code
     */
    public function generateCode($prefix = ''){
        $nextId = $this->getNextId($prefix);

        /* generate the increment id */
        $formatId = pow(10, self::CODE_LENGTH + 1) + $nextId;
        $formatId = (string) $formatId;
        $formatId = substr($formatId, 0-self::CODE_LENGTH);

        return $prefix . $formatId;
    }

    /**
     * Get next increment Id
     *
     * @param string $prefix
     * @return int
     */
    public function getNextId($prefix)
    {
        /** @var \Magestore\PurchaseOrderSuccess\Model\PurchaseOrderCode $model */
        $model = $this->purchaseOrderCodeFactory->create();
        $model->getResource()->load($model, $prefix, 'code');
        $nextId = $model->getCurrentId() + 1;
        $model->setCurrentId($nextId);
        if (!$model->getId()) {
            $model->setCode($prefix);
            $model->setId(null);
        }
        $model->getResource()->save($model);
        return $nextId;
    }
}