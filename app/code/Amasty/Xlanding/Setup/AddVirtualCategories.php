<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Setup;

use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Amasty\Xlanding\Api\Data\PageInterface;

/**
 * Class AddVirtualCategories
 * @package Amasty\Xlanding\Setup
 */
class AddVirtualCategories
{
    /**
     * @var \Magento\Eav\Setup\EavSetup
     */
    private $eavSetup;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    public function __construct(
        \Magento\Eav\Setup\EavSetup $eavSetup,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->eavSetup = $eavSetup;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @param \Magento\Setup\Module\DataSetup $setup
     */
    public function execute(\Magento\Setup\Module\DataSetup $setup)
    {
        $this->eavSetup->addAttribute(
            Category::ENTITY,
            PageInterface::IS_CATEGORY_DYNAMIC,
            [
                'type' => 'int',
                'label' => 'Is dynamic category',
                'visible' => true,
                'input' => null,
                'default' => 0,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'note' => "Get products from Landing Page",
                'group' => 'General Information',
                'sort_order' => 900,
            ]
        );

        $this->eavSetup->addAttribute(
            Category::ENTITY,
            PageInterface::DYNAMIC_CATEGORY_PAGE_ID,
            [
                'type' => 'int',
                'label' => 'Landing Page Id',
                'visible' => true,
                'input' => null,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'default' => 0,
                'group' => 'General Information',
                'sort_order' => 901,
            ]
        );

        $this->eavSetup->updateAttribute(
            Category::ENTITY,
            PageInterface::IS_CATEGORY_DYNAMIC,
            'frontend_input',
            null
        );
        $this->eavSetup->updateAttribute(
            Category::ENTITY,
            PageInterface::DYNAMIC_CATEGORY_PAGE_ID,
            'frontend_input',
            null
        );
        $this->eavConfig->clear();

        $connection = $setup->getConnection();
        $tableName = $setup->getTable('amasty_xlanding_page');
        if (!$connection->tableColumnExists($tableName, PageInterface::DYNAMIC_CATEGORY_ID)) {
            $connection->addColumn(
                $tableName,
                PageInterface::DYNAMIC_CATEGORY_ID,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Dynamic Category Id'
                ]
            );
        }
    }
}
