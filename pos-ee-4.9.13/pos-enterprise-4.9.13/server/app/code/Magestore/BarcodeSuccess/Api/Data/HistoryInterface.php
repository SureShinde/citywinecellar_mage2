<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Api\Data;

/**
 * @api
 */
interface HistoryInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const ID = 'id';

    const CREATED_AT = 'created_at';

    const CREATED_BY = 'created_by';

    const REASON = 'reason';

    const TOTAL_QTY = 'total_qty';

    const TYPE = 'type';

    /**#@-*/

    /**
     * History id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set history id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * get created at
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * get created by
     *
     * @return string|null
     */
    public function getCreatedBy();

    /**
     * Set created by
     *
     * @param string $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy);

    /**
     * get reason
     *
     * @return string|null
     */
    public function getReason();

    /**
     * Set reason
     *
     * @param string $reason
     * @return $this
     */
    public function setReason($reason);

    /**
     * get total qty
     *
     * @return string|null
     */
    public function getTotalQty();

    /**
     * Set total qty
     *
     * @param string $totalQty
     * @return $this
     */
    public function setTotalQty($totalQty);

    /**
     * get type
     *
     * @return string|null
     */
    public function getType();

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);


}
