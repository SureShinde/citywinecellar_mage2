<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Shift;

/**
 * Interface CashTransactionRepositoryInterface
 * @package Magestore\Webpos\Api\Shift
 */
interface CashTransactionRepositoryInterface
{

    /**
     * @param  \Magestore\Webpos\Api\Data\Shift\CashTransactionInterface[] $cashTransactions
     * @return  \Magestore\Webpos\Api\Data\Shift\ShiftInterface $shipInterface
     */
    public function save($cashTransactions);


}