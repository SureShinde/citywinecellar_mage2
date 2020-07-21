<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Api\Data;

interface HistoryInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ID = 'id';
    const CUSTOMER_ID = 'customer_id';
    const DATE = 'date';
    const ACTION_ID = 'action_id';
    const PARAMS = 'params';
    /**#@-*/

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return \Amasty\Rewards\Api\Data\HistoryInterface
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     *
     * @return \Amasty\Rewards\Api\Data\HistoryInterface
     */
    public function setCustomerId($customerId);

    /**
     * @return string
     */
    public function getDate();

    /**
     * @param string $date
     *
     * @return \Amasty\Rewards\Api\Data\HistoryInterface
     */
    public function setDate($date);

    /**
     * @return int
     */
    public function getActionId();

    /**
     * @param int $actionId
     *
     * @return \Amasty\Rewards\Api\Data\HistoryInterface
     */
    public function setActionId($actionId);

    /**
     * @return string
     */
    public function getParams();

    /**
     * @param string $params
     *
     * @return \Amasty\Rewards\Api\Data\HistoryInterface
     */
    public function setParams($params);
}
