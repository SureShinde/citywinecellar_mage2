<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\Rule\Condition;

use Magento\Framework\DB\Select;

class Qty extends AbstractCondition
{
    /**
     * @var string
     */
    protected $_inputType = 'numeric';

    /**
     * @var \Magento\CatalogInventory\Model\ResourceModel\Stock\Status
     */
    private $stockStatus;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockStatus,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->stockStatus = $stockStatus;
        $this->moduleManager = $moduleManager;
        parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productRepository,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data
        );
    }

    public function getAttributeElementHtml()
    {
        return __('Qty');
    }

    protected function _getAttributeCode()
    {
        return 'qty';
    }

    /**
     * @param Select $select
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    protected function getQtyColumn($select)
    {
        $fromTables = $select->getPart(Select::FROM);
        $catalogInventoryTable = $this->stockStatus->getMainTable();
        if ($this->isMsiEnabled() && $fromTables['stock_status']['tableName'] != $catalogInventoryTable) {
            $qtyColumn = 'quantity';
        } else {
            $qtyColumn = 'qty';
            $fromTables['stock_status']['joinCondition'] = preg_replace(
                '@(stock_status.website_id=)\d+@',
                '$1 0',
                $fromTables['stock_status']['joinCondition']
            );
            $select->setPart(Select::FROM, $fromTables);
        }

        return $qtyColumn;
    }

    /**
     * @param Select $select
     * @return bool
     * @throws \Zend_Db_Select_Exception
     */
    protected function isStockStatusJoined($select)
    {
        $fromTables = $select->getPart(Select::FROM);

        return isset($fromTables['stock_status']);
    }

    /**
     * @param Select $select
     * @return AbstractCondition|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    public function collectValidatedAttributes($select)
    {
        $value = $this->getValue();
        $operator = $this->getOperatorForValidate();

        if (!$this->isStockStatusJoined($select)) {
            $this->stockStatus->addStockStatusToSelect($select, $this->getStoreManager()->getWebsite());
        }

        $this->_condition = $this->getOperatorCondition($this->getQtyColumn($select), $operator, $value);

        $select->where(
            'e.type_id = ?',
            \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
        )->where(
            $this->_condition
        );
    }

    /**
     * @return bool
     */
    private function isMsiEnabled()
    {
        return $this->moduleManager->isEnabled('Magento_Inventory');
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        return 'string';
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }
}
