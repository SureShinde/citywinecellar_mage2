<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Appadmin\Block\Adminhtml\Staff\Role\Edit\Tab;

/**
 * Edit role Form
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_objectManager;

    /**
     * Form constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare Layout
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('page.title')->setPageTitle($this->getPageTitle());
    }

    /**
     * Prepare Form
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_role');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Role Information')]);

        if ($model->getRoleId()) {
            $fieldset->addField('role_id', 'hidden', ['name' => 'role_id', 'value' => $model->getRoleId()]);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'label' => __('Role Name'),
                'class' => 'required-entry',
                'required' => true,
                'maxlength' => 127,
                'name' => 'name',
                'disabled' => false,
            ]
        );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'label' => __('Description'),
                'name' => 'description',
                'maxlength' => 255,
                'disabled' => false,
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Get Role
     *
     * @return mixed
     */
    public function getRole()
    {
        return $this->_coreRegistry->registry('current_role');
    }

    /**
     * Get Page Title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getPageTitle()
    {
        return $this->getRole()->getId()
            ? __("Edit Role %1", $this->escapeHtml($this->getRole()->getDisplayName()))
            : __('New Role');
    }

    /**
     * Get Tab Label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Role Information');
    }

    /**
     * Get Tab Title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Role Information');
    }

    /**
     * Can Show Tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Is Hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
