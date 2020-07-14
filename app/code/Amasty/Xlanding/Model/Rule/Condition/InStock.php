<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\Rule\Condition;

use Magento\Framework\DB\Select;

class InStock extends AbstractCondition
{
    /**
     * @var string
     */
    protected $_inputType = 'select';

    /**
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $_string;

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
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockStatus,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_string = $string;
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
        return __('In Stock');
    }

    public function getInputType()
    {
        return 'select';
    }

    public function getValueElementType()
    {
        return 'select';
    }

    protected function _getAttributeCode()
    {
        return 'in_stock';
    }

    protected function _prepareValueOptions()
    {
        $selectReady = $this->getData('value_select_options');
        $hashedReady = $this->getData('value_option');

        $selectOptions = [
            ['value' => 1, 'label' => 'Yes'],
            ['value' => 0, 'label' => 'No']
        ];

        $this->_setSelectOptions($selectOptions, $selectReady, $hashedReady);

        return $this;
    }

    public function collectValidatedAttributes($select)
    {
        $from = $select->getPart(\Zend_Db_Select::FROM);
        $where = $select->getPart(\Zend_Db_Select::WHERE);

        if ($this->getValue() && isset($from['stock_index'])) {
            //select already has stock condition
            return $select;
        }

        //if stock condition exists and value = 0 ( out of stock products )
        if (!$this->getValue() && isset($from['stock_index']) && $from['stock_index']['joinCondition']) {
            $from['stock_index']['joinCondition'] = str_replace(
                '`stock_status` = 1',
                '`stock_status` = 0',
                $from['stock_index']['joinCondition']
            );
            $select->setPart(\Zend_Db_Select::FROM, $from);
            return $select;
        }

        //situation where select doesn't have stock index table - deprecated - remove in future
        if (isset($from['stock_index'])) {
            unset($from['stock_index']);
            foreach ($where as $idx => $condition) {
                if ($this->_string->strpos($condition, 'stock_index') !== false) {
                    unset($where[$idx]);
                }
            }

            $select->setPart(\Zend_Db_Select::FROM, $from);
            $select->setPart(\Zend_Db_Select::WHERE, $where);
        }

        if (!$this->isStockStatusJoined($select)) {
            $this->stockStatus->addStockStatusToSelect($select, $this->getStoreManager()->getWebsite());
        }

        $this->prepareCondition($select);
        $select->distinct(true);
        $select->where($this->_condition);
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
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    protected function getStockColumn($select)
    {
        $fromTables = $select->getPart(Select::FROM);
        $catalogInventoryTable = $this->stockStatus->getMainTable();
        if ($this->isMsiEnabled() && $fromTables['stock_status']['tableName'] != $catalogInventoryTable) {
            $qtyColumn = 'is_salable';
        } else {
            $qtyColumn = 'stock_status';
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
     * @return bool
     */
    private function isMsiEnabled()
    {
        return $this->moduleManager->isEnabled('Magento_Inventory');
    }

    /**
     * @param $select
     * @return \Amasty\VisualMerch\Model\Rule\Condition\InStock
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    protected function prepareCondition($select)
    {
        $value     = $this->getValue();
        $operator  = $this->getOperatorForValidate();

        $condition = $this->getOperatorCondition($this->getStockColumn($select), $operator, $value);

        $this->_condition = $condition;

        return $this;
    }
}
