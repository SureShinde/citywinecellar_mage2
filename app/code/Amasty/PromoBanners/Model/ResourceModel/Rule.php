<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Model\ResourceModel;

class Rule extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('amasty_banner_rule', 'id');
    }

    public function assignProducts($ruleId, $productIds)
    {
        $connection = $this->getConnection();

        $productsTable = $this->getTable('amasty_banner_rule_products');

        $connection->delete($productsTable, ['rule_id=?' => $ruleId]);

        if (!$productIds) {
            return false;
        }

        $data = [];

        foreach ($productIds as $id) {
            $data [] = [
                'rule_id'    => $ruleId,
                'product_id' => $id
            ];
        }

        $connection->insertMultiple($productsTable, $data);

        return true;
    }

    public function getProducts($ruleId)
    {
        $connection = $this->getConnection();

        $sql = $connection->select()
            ->from($this->getTable('amasty_banner_rule_products'), 'product_id')
            ->where('rule_id = ?', $ruleId);

        return $connection->fetchCol($sql);
    }

    public function getAttributes()
    {
        $db = $this->getConnection();
        $tbl = $this->getTable('amasty_banner_attribute');

        $select = $db->select()->from($tbl, new \Zend_Db_Expr('DISTINCT code'));
        return $db->fetchCol($select);
    }

    public function saveAttributes($id, $attributes)
    {
        $db = $this->getConnection();
        $tbl = $this->getTable('amasty_banner_attribute');

        $db->delete($tbl, ['rule_id=?' => $id]);

        $data = [];
        foreach ($attributes as $code) {
            $data[] = [
                'rule_id' => $id,
                'code'    => $code,
            ];
        }
        $db->insertMultiple($tbl, $data);

        return $this;
    }
}
