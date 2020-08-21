<?php
/**
 * Created by Magestore Developer.
 * Date: 1/26/2016
 * Time: 4:09 PM
 * Set final price to product
 */

namespace Magestore\Rewardpoints\Observer\Webpos\Order;

use Magento\Framework\Event\ObserverInterface;

/**
 * Observer -  Webpos Order Collection Load Before
 */
class WebposOrderCollectionLoadBefore implements ObserverInterface
{
    /**
     * @inheritDoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer['collection'];
        $collection->getSelect()
            ->joinLeft(
                ['creditmemo' => $collection->getTable('sales_creditmemo')],
                'main_table.entity_id = creditmemo.order_id',
                [
                    'creditmemo_rewardpoints_earn' => 'SUM(creditmemo.rewardpoints_earn)',
                    'creditmemo_rewardpoints_discount' => 'SUM(creditmemo.rewardpoints_discount)',
                    'creditmemo_rewardpoints_base_discount' => 'SUM(creditmemo.rewardpoints_base_discount)',
                ]
            );
        $collection->getSelect()->group('main_table.entity_id');
        return $this;
    }
}
