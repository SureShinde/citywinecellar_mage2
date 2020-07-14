<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created by PhpStorm.
 * User: steve
 * Date: 06/06/2016
 * Time: 13:42
 */

namespace Magestore\Webpos\Model\Shift;

use Magento\Framework\Exception\StateException;
use Magestore\Webpos\Api\Data\Shift\CashTransactionInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magestore\Webpos\Api\Shift\ShiftRepositoryInterface;


class CashTransactionRepository implements \Magestore\Webpos\Api\Shift\CashTransactionRepositoryInterface
{
    /** @var  $cashTransactionResource \Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction */
    protected $cashTransactionResource;

    /** @var  \Magestore\Webpos\Model\Shift\ShiftFactory */
    protected $_shiftFactory;

    /** @var  \Magestore\Webpos\Helper\Shift */
    protected $_shiftHelper;

    /**
     * CashTransactionRepository constructor.
     * @param \Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction $cashTransactionResource
     * @param ShiftFactory $shiftFactory
     * @param \Magestore\Webpos\Helper\Shift $shiftHelper
     */
    public function __construct(
        \Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction $cashTransactionResource,
        \Magestore\Webpos\Model\Shift\ShiftFactory $shiftFactory,
        \Magestore\Webpos\Helper\Shift $shiftHelper
    )
    {
        $this->cashTransactionResource = $cashTransactionResource;
        $this->_shiftFactory = $shiftFactory;
        $this->_shiftHelper = $shiftHelper;
    }


    /**
     * @param CashTransactionInterface[] $cashTransactions
     * @return array|CashTransactionInterface
     * @throws CouldNotSaveException
     * @throws StateException
     */
    public function save($cashTransactions)
    {
        $shiftModel = null;
        if ($cashTransactions && !empty($cashTransactions)) {
            /** @var CashTransactionInterface $cashTransaction */
            foreach ($cashTransactions as $cashTransaction) {
                $shiftIncrementId = $cashTransaction->getShiftIncrementId();
                if (!$shiftModel || !$shiftModel->getShiftIncrementId() ||
                    $shiftModel->getShiftIncrementId() != $shiftIncrementId) {
                    $shiftModel = $this->_shiftFactory->create()->load($shiftIncrementId, "shift_increment_id");
                    if (!$shiftModel->getShiftIncrementId()) {
                        throw new StateException(__('Shift increment id is required'));
                    }
                }
                try {
                    $cashTransaction->setShiftIncrementId($shiftIncrementId);
                    $this->cashTransactionResource->save($cashTransaction);
                } catch (\Exception $exception) {
                    throw new CouldNotSaveException(__($exception->getMessage()));
                }
            }
        }
        if ($shiftModel) {
            $shiftModel->setUpdatedAt(date('Y-m-d H:i:s'))->save();
            return $shiftModel;
        }
        return [];
    }
}