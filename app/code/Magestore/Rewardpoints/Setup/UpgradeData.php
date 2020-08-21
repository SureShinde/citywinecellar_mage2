<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Rewardpoints\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Reward points - Upgrade data
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    protected $orderFactory;

    /**
     * @var []
     */
    protected $_calculators;

    /**
     * @var \Magento\Framework\Math\CalculatorFactory
     */
    protected $_calculatorFactory;

    /**
     * @var \Magento\Framework\App\ProductMetadata
     */
    protected $productMetadata;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * UpgradeData constructor.
     *
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\Math\CalculatorFactory $_calculatorFactory
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Math\CalculatorFactory $_calculatorFactory,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\App\State $appState
    ) {
        $this->orderFactory = $orderFactory;
        $this->_calculatorFactory = $_calculatorFactory;
        $this->productMetadata = $productMetadata;
        $this->_appState = $appState;
    }

    /**
     * @inheritDoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $version = $this->productMetadata->getVersion();
            try {
                if (version_compare($version, '2.2.0', '>=')) {
                    $this->_appState->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
                } else {
                    $this->_appState->setAreaCode('admin');
                }
            } catch (\Exception $e) {
                $this->_appState->getAreaCode();
            }
            $this->convertOrder($setup);
        }
    }

    /**
     * Convert Order
     *
     * @param ModuleDataSetupInterface $setup
     * @throws \Exception
     */
    public function convertOrder(ModuleDataSetupInterface $setup)
    {
        $orderTable = $setup->getTable('sales_order');
        $select = $setup->getConnection()->select();
        $select->from(['main_table' => $orderTable], ['entity_id'])
            ->where('rewardpoints_base_discount > ?', 0);
        $data = $setup->getConnection()->fetchAll($select);
        foreach ($data as $item) {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->orderFactory->create()->load($item['entity_id']);
            $orderItems = $order->getAllItems();
            $store = $order->getStore();
            $totalItemsRewardpointsBaseDiscountInvoiced = $totalItemsRewardpointsDiscountInvoiced
                = $totalItemsRewardpointsBaseDiscountRefunded = $totalItemsRewardpointsDiscountRefunded = 0;
            foreach ($orderItems as $orderItem) {
                $qtyOrdered = $orderItem->getQtyOrdered();
                $qtyInvoiced = $orderItem->getQtyInvoiced();
                $qtyRefunded = $orderItem->getQtyRefunded();
                $rewardpointsBaseDiscount = $this->roundPrice($orderItem->getRewardpointsBaseDiscount(), true, $store);
                $rewardpointsDiscount = $this->roundPrice($orderItem->getRewardpointsDiscount(), true, $store);
                $baseDiscountAmount = $orderItem->getBaseDiscountAmount();
                $discountAmount = $orderItem->getDiscountAmount();
                $baseDiscountInvoiced = $orderItem->getBaseDiscountInvoiced();
                $discountInvoiced = $orderItem->getDiscountInvoiced();
                $baseDiscountRefunded = $orderItem->getBaseDiscountRefunded() ?
                    $orderItem->getBaseDiscountRefunded() : 0;
                $discountRefunded = $orderItem->getDiscountRefunded() ? $orderItem->getDiscountRefunded() : 0;
                $baseRewardpointsDiscountInvoiced = $rewardpointsBaseDiscount / $qtyOrdered * $qtyInvoiced;
                $rewardpointsDiscountInvoiced = $rewardpointsDiscount / $qtyOrdered * $qtyInvoiced;
                $baseRewardpointsDiscountRefunded = $rewardpointsBaseDiscount / $qtyOrdered * $qtyRefunded;
                $rewardpointsDiscountRefunded = $rewardpointsDiscount / $qtyOrdered * $qtyRefunded;
                $orderItem->setBaseDiscountAmount(
                    $baseDiscountAmount + $this->roundPrice($rewardpointsBaseDiscount, true, $store)
                );
                $orderItem->setDiscountAmount(
                    $discountAmount + $this->roundPrice($rewardpointsDiscount, true, $store)
                );
                $orderItem->setBaseDiscountInvoiced(
                    $baseDiscountInvoiced + $this->roundPrice($baseRewardpointsDiscountInvoiced, true, $store)
                );
                $orderItem->setDiscountInvoiced(
                    $discountInvoiced + $this->roundPrice($rewardpointsDiscountInvoiced, true, $store)
                );
                $orderItem->setBaseDiscountRefunded(
                    $baseDiscountRefunded + $this->roundPrice($baseRewardpointsDiscountRefunded, true, $store)
                );
                $orderItem->setBaseDiscountRefunded(
                    $discountRefunded + $this->roundPrice($rewardpointsDiscountRefunded, true, $store)
                );
                $orderItem->setMagestoreBaseDiscount(
                    $orderItem->getMagestoreBaseDiscount() + $rewardpointsBaseDiscount
                );
                $orderItem->setMagestoreDiscount($orderItem->getMagestoreDiscount() + $rewardpointsDiscount);
                $totalItemsRewardpointsBaseDiscountInvoiced += $baseRewardpointsDiscountInvoiced;
                $totalItemsRewardpointsDiscountInvoiced += $rewardpointsDiscountInvoiced;
                $totalItemsRewardpointsBaseDiscountRefunded += $baseRewardpointsDiscountRefunded;
                $totalItemsRewardpointsDiscountRefunded += $rewardpointsDiscountRefunded;
                $orderItem->save();
            }
            $baseDiscountAmount = $order->getBaseDiscountAmount();
            $discountAmount = $order->getDiscountAmount();
            $baseDiscountInvoiced = $order->getBaseDiscountInvoiced();
            $discountInvoiced = $order->getDiscountInvoiced();
            $baseDiscountRefunded = $order->getBaseDiscountRefunded() ? $order->getBaseDiscountRefunded() : 0;
            $discountRefunded = $order->getDiscountRefunded() ? $order->getDiscountRefunded() : 0;
            $rewardpointsBaseDiscount = $order->getRewardpointsBaseDiscount();
            $rewardpointsDiscount = $order->getRewardpointsDiscount();
            $order->setBaseDiscountAmount($baseDiscountAmount - $rewardpointsBaseDiscount);
            $order->setDiscountAmount($discountAmount - $rewardpointsDiscount);
            $order->setBaseDiscountInvoiced(
                $baseDiscountInvoiced - $this->roundPrice($totalItemsRewardpointsBaseDiscountInvoiced, true, $store)
            );
            $order->setDiscountInvoiced(
                $discountInvoiced - $this->roundPrice($totalItemsRewardpointsDiscountInvoiced, true, $store)
            );
            $order->setBaseDiscountRefunded(
                $baseDiscountRefunded - $this->roundPrice($totalItemsRewardpointsBaseDiscountRefunded, true, $store)
            );
            $order->setDiscountRefunded(
                $discountRefunded - $this->roundPrice($totalItemsRewardpointsDiscountRefunded, true, $store)
            );
            $order->save();
        }
    }

    /**
     * Round price considering delta
     *
     * @param float $price
     * @param bool $negative Indicates if we perform addition (true) or subtraction (false) of rounded value
     * @param \Magento\Store\Model\Store $store
     * @return float
     */
    public function roundPrice($price, $negative, $store)
    {
        $store->getStoreId();
        if ($price) {
            if (!isset($this->_calculators[$store->getStoreId()])) {
                $this->_calculators[$store->getStoreId()] = $this->_calculatorFactory->create(['scope' => $store]);
            }
            $price = $this->_calculators[$store->getStoreId()]->deltaRound($price, $negative);
        }
        return $price;
    }
}
