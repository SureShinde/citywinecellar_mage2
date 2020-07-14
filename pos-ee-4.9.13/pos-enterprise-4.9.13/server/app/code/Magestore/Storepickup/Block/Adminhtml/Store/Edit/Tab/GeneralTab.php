<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Storepickup
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Storepickup\Block\Adminhtml\Store\Edit\Tab;

/**
 * General Tab.
 *
 * @category Magestore
 * @package  Magestore_Storepickup
 * @module   Storepickup
 * @author   Magestore Developer
 */
class GeneralTab extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magestore\Storepickup\Helper\Data
     */
    protected $storePickUpHelper;

    /**
     * GeneralTab constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magestore\Storepickup\Helper\Data $storePickUpHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magestore\Storepickup\Helper\Data $storePickUpHelper,
        array $data = []
    )
    {
        $this->_moduleManager = $moduleManager;
        $this->_objectManager = $objectManager;
        $this->storePickUpHelper = $storePickUpHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->getRegistryModel();
        $isMSISourceEnable = $this->storePickUpHelper->isMSISourceEnable();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('store_');

        /*
         * General Field Set
         */
        $fieldset = $form->addFieldset(
            'general_fieldset',
            [
                'legend' => __('General Information'),
                'collapsable' => true,
            ]
        );

        if ($model->getId()) {
            $fieldset->addField('storepickup_id', 'hidden', ['name' => 'storepickup_id']);
        }

        if ($isMSISourceEnable) {
            $options = ['' => __('---Please select---')];
            if (!$model->getId()) {
                $options['Create a new one'] = __('Create a new one');
            }
            /** @var \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository */
            $sourceRepository = $this->_objectManager->get('Magento\InventoryApi\Api\SourceRepositoryInterface');
            $sources = $sourceRepository->getList()->getItems();
            foreach ($sources as $source) {
                $options[$source->getSourceCode()] = $source->getName();
            }
            $fieldset->addField(
                'source_code',
                'select',
                [
                    'label' => __('Source Selection'),
                    'title' => __('Source Selection'),
                    'name' => 'source_code',
                    'options' => $options,
                    'required' => true,
                ]
            );

            $fieldset->addField(
                'new_source_code',
                'text',
                [
                    'label' => __('Source Code'),
                    'title' => __('Source Code'),
                    'name' => 'new_source_code',
                    'required' => true,
                    'class' => 'no-whitespace'
                ]
            );
            $htmlIdPrefix = $form->getHtmlIdPrefix();
            $this->setChild(
                'form_after',
                $this->getLayout()->createBlock(
                    \Magento\Backend\Block\Widget\Form\Element\Dependence::class
                )->addFieldMap(
                    $htmlIdPrefix . "source_code",
                    'source_code'
                )->addFieldMap(
                    $htmlIdPrefix . "new_source_code",
                    'new_source_code'
                )->addFieldDependence(
                    'new_source_code',
                    'source_code',
                    'Create a new one'
                )
            );
        }

        $fieldset->addField(
            'store_name',
            'text',
            [
                'name' => 'store_name',
                'label' => __('Store Name'),
                'title' => __('Store Name'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'description',
            'editor',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'wysiwyg' => true,
            ]
        );

        if ($this->_moduleManager->isEnabled('Magestore_InventorySuccess') && !$isMSISourceEnable) {
            $options = ['value' => '', 'label' => __('Select a warehouse')];
            $optionArray = $this->_objectManager->create('Magestore\InventorySuccess\Model\Source\Adminhtml\Warehouse')->toOptionArray();
            foreach ($optionArray as $_option) {
                $options[$_option['value']] = $_option['label'];
            }
            $fieldset->addField(
                'warehouse_id',
                'select',
                [
                    'label' => __('Link to warehouse'),
                    'title' => __('Link to warehouse'),
                    'name' => 'warehouse_id',
                    'options' => $options,
                ]
            );
        }

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'options' => \Magestore\Storepickup\Model\Status::getAvailableStatuses(),
            ]
        );

        $fieldset->addField(
            'link',
            'text',
            [
                'name' => 'link',
                'label' => __('Store\'s Link'),
                'title' => __('Store\'s Link'),
            ]
        );

        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
            ]
        );

        /*
         * Contact Field Set
         */
        $fieldset = $form->addFieldset(
            'contact_fieldset',
            [
                'legend' => __('Contact Information'),
                'collapsable' => true,
            ]
        );

        $fieldset->addField(
            'contact_name',
            'text',
            [
                'name' => 'contact_name',
                'label' => __('Contact Name'),
                'title' => __('Contact Name'),
            ]
        );

        $fieldset->addField(
            'email',
            'text',
            [
                'name' => 'email',
                'label' => __('Email'),
                'title' => __('Email'),
            ]
        );

        $fieldset->addField(
            'phone',
            'text',
            [
                'name' => 'phone',
                'label' => __('Phone'),
                'title' => __('Phone'),
            ]
        );

        $fieldset->addField(
            'fax',
            'text',
            [
                'name' => 'fax',
                'label' => __('Fax'),
                'title' => __('Fax'),
            ]
        );

        $fieldset = $form->addFieldset(
            'owner_information',
            [
                'legend' => __('Owner Information'),
                'collapsable' => true,
            ]
        );

        $fieldset->addField(
            'owner_name',
            'text',
            [
                'name' => 'owner_name',
                'label' => __("Owner's name"),
                'title' => __("Owner's name"),
            ]
        );

        $fieldset->addField(
            'owner_email',
            'text',
            [
                'name' => 'owner_email',
                'label' => __('Owner\' Email'),
                'title' => __('Owner\' Email'),
            ]
        );

        $fieldset->addField(
            'owner_phone',
            'text',
            [
                'name' => 'owner_phone',
                'label' => __('Owner\' Phone'),
                'title' => __('Owner\' Phone'),
            ]
        );

        /*
         * Meta Information Field Set
         */
        $fieldset = $form->addFieldset(
            'meta_fieldset',
            [
                'legend' => __('Meta Information'),
                'collapsable' => true,
            ]
        );

        $fieldset->addField(
            'rewrite_request_path',
            'text',
            [
                'name' => 'rewrite_request_path',
                'label' => __('URL Key'),
                'title' => __('URL Key'),
                'class' => 'validate-identifier'
            ]
        );

        $fieldset->addField(
            'meta_title',
            'text',
            [
                'name' => 'meta_title',
                'label' => __('Meta Title'),
                'title' => __('Meta Title'),
            ]
        );

        $fieldset->addField(
            'meta_keywords',
            'textarea',
            [
                'name' => 'meta_keywords',
                'label' => __('Meta Keywords'),
                'title' => __('Meta Keywords'),
            ]
        );
        $fieldset->addField(
            'meta_description',
            'textarea',
            [
                'name' => 'meta_description',
                'label' => __('Meta Description'),
                'title' => __('Meta Description'),
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * get registry model.
     *
     * @return \Magestore\Storepickup\Model\Store
     */
    public function getRegistryModel()
    {
        return $this->_coreRegistry->registry('storepickup_store');
    }

    /**
     * Return Tab label.
     *
     * @return string
     *
     * @api
     */
    public function getTabLabel()
    {
        return __('General information');
    }

    /**
     * Return Tab title.
     *
     * @return string
     *
     * @api
     */
    public function getTabTitle()
    {
        return __('General information');
    }

    /**
     * Can show tab in tabs.
     *
     * @return bool
     *
     * @api
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden.
     *
     * @return bool
     *
     * @api
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Return dependency block object
     *
     * @return \Magento\Backend\Block\Widget\Form\Element\Dependence
     */
    public function _getDependence()
    {
        if (!$this->getChildBlock('element_dependence')) {
            $this->addChild('element_dependence', \Magento\Backend\Block\Widget\Form\Element\Dependence::class);
        }
        return $this->getChildBlock('element_dependence');
    }
}
