<?php

namespace Magestore\Webpos\Api\Data\Sales\Order\Creditmemo;

interface ItemInterface extends \Magento\Sales\Api\Data\CreditmemoItemInterface {

    const BACK_TO_STOCK = 'back_to_stock';
    const POS_LOCATION_ID = 'pos_location_id';

    /**
     * Get back to stock
     *
     * @return boolean|null
     */
    public function getBackToStock();

    /**
     * Set back to stock
     *
     * @param boolean|null $backToStock
     * @return \Magestore\Webpos\Api\Data\Sales\Order\Creditmemo\ItemInterface
     */
    public function setBackToStock($backToStock);

    /**
     * Get pos location id
     *
     * @return int|null
     */
    public function getPosLocationId();

    /**
     * Set pos location id
     *
     * @param int|null $posLocationId
     * @return \Magestore\Webpos\Api\Data\Sales\Order\Creditmemo\ItemInterface
     */
    public function setPosLocationId($posLocationId);


}