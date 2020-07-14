<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 * @package Magestore\Webpos\Setup
 */
class InstallData implements InstallDataInterface
{

    /**
     * @var \Magento\Eav\Model\Entity\Type
     */
    protected $_entityTypeModel;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    protected $_catalogAttribute;

    /**
     * @var \Magento\Eav\Setup\EavSetup
     */
    protected $_eavSetup;

    /**
     *
     * @param \Magento\Eav\Setup\EavSetup $eavSetup
     * @param \Magento\Eav\Model\Entity\Type $entityType
     * @param \Magento\Eav\Model\Entity\Attribute $catalogAttribute
     */
    public function __construct(
        \Magento\Eav\Setup\EavSetup $eavSetup,
        \Magento\Eav\Model\Entity\Type $entityType,
        \Magento\Eav\Model\Entity\Attribute $catalogAttribute
    ) {
        $this->_eavSetup = $eavSetup;
        $this->_entityTypeModel = $entityType;
        $this->_catalogAttribute = $catalogAttribute;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $entityTypeModel = $this->_entityTypeModel;
        $catalogAttributeModel = $this->_catalogAttribute;
        $installer = $this->_eavSetup;

        $setup->startSetup();

        $tax = $catalogAttributeModel->loadByCode('catalog_product', 'tax_class_id');
        $applyTo = explode(',', $tax->getData('apply_to'));
        $applyTo[] = 'giftvoucher';
        $taxApplyTo = implode(',', $applyTo);
        $tax->addData(array('apply_to' => $taxApplyTo))->save();

        $weight = $catalogAttributeModel->loadByCode('catalog_product', 'weight');
        $applyTo = explode(',', $weight->getData('apply_to'));
        $applyTo[] = 'giftvoucher';
        $weightApplyTo = implode(',', $applyTo);
        $weight->addData(array('apply_to' => $weightApplyTo))->save();

        $installer->removeAttribute(
            $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
            'gift_type'
        );
        $installer->removeAttribute(
            $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
            'gift_value'
        );
        $installer->removeAttribute(
            $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
            'gift_from'
        );
        $installer->removeAttribute(
            $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
            'gift_to'
        );
        $installer->removeAttribute(
            $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
            'gift_dropdown'
        );
        $installer->removeAttribute(
            $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
            'gift_price_type'
        );
        $installer->removeAttribute(
            $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
            'gift_price'
        );
        $installer->removeAttribute(
            $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
            'gift_template_ids'
        );

        $data = array(
            'group' => 'General',
            'type' => 'varchar',
            'input' => 'multiselect',
            'label' => 'Select Gift Card templates ',
            'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
            'frontend' => '',
            'source' => 'Magestore\Giftvoucher\Model\Source\TemplateOptions',
            'visible' => 1,
            'required' => 1,
            'user_defined' => 1,
            'used_for_price_rules' => 1,
            'position' => 2,
            'unique' => 0,
            'default' => '',
            'sort_order' => 100,
            'apply_to' => 'giftvoucher',
            'is_global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
            'is_required' => 1,
            'is_configurable' => 1,
            'is_searchable' => 0,
            'is_visible_in_advanced_search' => 0,
            'is_comparable' => 0,
            'is_filterable' => 0,
            'is_filterable_in_search' => 1,
            'is_used_for_promo_rules' => 1,
            'is_html_allowed_on_front' => 0,
            'is_visible_on_front' => 0,
            'used_in_product_listing' => 1,
            'used_for_sort_by' => 0,
        );
        $installer->addAttribute(
            $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
            'gift_template_ids',
            $data
        );
        $giftTemplateIds = $catalogAttributeModel->loadByCode('catalog_product', 'gift_template_ids');
        $giftTemplateIds->addData($data)->save();


        $data['group'] = 'Advanced Pricing';
        $data['type'] = 'int';
        $data['input'] = 'select';
        $data['label'] = 'Type of Gift Card value';
        $data['backend'] = '';
        $data['required'] = 1;
        $data['visible'] = 1;
        $data['source'] = 'Magestore\Giftvoucher\Model\Source\GiftType';
        $installer->addAttribute(
            $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
            'gift_type',
            $data
        );
        $giftType = $catalogAttributeModel->loadByCode('catalog_product', 'gift_type');
        $giftType->addData($data)->save();


        $data['type'] = 'decimal';
        $data['input'] = 'price';
        $data['class'] = 'validate-number';
        $data['label'] = 'Gift Card value';
        $data['position'] = 4;
        $data['sort_order'] = 103;
        $data['source'] = '';
        $data['required'] = 0;
        $data['visible'] = 1;
        $installer->addAttribute(
            $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
            'gift_value',
            $data
        );
        $giftValue = $catalogAttributeModel->loadByCode('catalog_product', 'gift_value');
        $giftValue->addData($data)->save();


        $data['type'] = 'text';
        $data['input'] = 'text';
        $data['class'] = '';
        $data['label'] = 'Gift Card price';
        $data['position'] = 13;
        $data['sort_order'] = 110;
        $data['required'] = 0;
        $data['visible'] = 1;
        $installer->addAttribute(
            $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
            'gift_price',
            $data
        );
        $giftPrice = $catalogAttributeModel->loadByCode('catalog_product', 'gift_price');
        $giftPrice->addData($data)->save();


        $data['type'] = 'decimal';
        $data['input'] = 'price';
        $data['label'] = 'Minimum Gift Card value';
        $data['class'] = 'validate-number';
        $data['position'] = 10;
        $data['sort_order'] = 107;
        $data['note'] = '';
        $data['required'] = 0;
        $data['visible'] = 1;
        $installer->addAttribute(
            $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
            'gift_from',
            $data
        );
        $giftFrom = $catalogAttributeModel->loadByCode('catalog_product', 'gift_from');
        $giftFrom->addData($data)->save();


        $data['label'] = 'Maximum Gift Card value';
        $data['class'] = 'validate-number';
        $data['position'] = 11;
        $data['sort_order'] = 108;
        $data['required'] = 0;
        $data['visible'] = 1;
        $installer->addAttribute(
            $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
            'gift_to',
            $data
        );
        $giftTo = $catalogAttributeModel->loadByCode('catalog_product', 'gift_to');
        $giftTo->addData($data)->save();


        $data['type'] = 'varchar';
        $data['input'] = 'text';
        $data['label'] = 'Gift Card values';
        $data['class'] = '';
        $data['position'] = 12;
        $data['sort_order'] = 109;
        $data['backend_type'] = 'text';
        $data['note'] = __('Seperated by comma, e.g. 10,20,30');
        $data['required'] = 0;
        $data['visible'] = 1;
        $installer->addAttribute(
            $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
            'gift_dropdown',
            $data
        );
        $giftDropdown = $catalogAttributeModel->loadByCode('catalog_product', 'gift_dropdown');
        $giftDropdown->addData($data)->save();


        $data['type'] = 'int';
        $data['input'] = 'select';
        $data['label'] = 'Type of Gift Card price';
        $data['class'] = '';
        $data['required'] = 1;
        $data['visible'] = 1;
        $data['position'] = 12;
        $data['sort_order'] = 109;
        $data['backend_type'] = 'text';
        $data['note'] = __('Gift Card price is the same as Gift Card value by default.');
        $data['source'] = 'Magestore\Giftvoucher\Model\Source\GiftPriceType';
        $installer->addAttribute(
            $entityTypeModel->loadByCode('catalog_product')->getData('entity_type_id'),
            'gift_price_type',
            $data
        );
        $giftPriceType = $catalogAttributeModel->loadByCode('catalog_product', 'gift_price_type');
        $giftPriceType->addData($data)->save();

        $setup->endSetup();
    }
}
