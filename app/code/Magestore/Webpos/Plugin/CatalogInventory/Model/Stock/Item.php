<?php
namespace Magestore\Webpos\Plugin\CatalogInventory\Model\Stock;

class Item
{
    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \Magento\Framework\Registry $registry
    )
    {
        $this->registry = $registry;
    }

    /**
     * @param \Magento\CatalogInventory\Model\Stock\Item $stockItem
     * @param $isBackOrdered
     * @return bool
     */
    public function afterGetBackorders(
        \Magento\CatalogInventory\Model\Stock\Item $stockItem,
        $isBackOrdered
    )
    {
        if ($this->registry->registry('create_order_webpos')) {
            return \Magento\CatalogInventory\Model\Stock::BACKORDERS_YES_NONOTIFY;
        }
        return $isBackOrdered;
    }
}