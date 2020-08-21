<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Denomination\Edit\Tab;

/**
 * Block denomination form
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

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
        $this->_eventManager = $context->getEventManager();
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare layout
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('page.title')->setPageTitle($this->getPageTitle());
    }

    /**
     * Prepare form
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_denomination');
        $data = [];
        if ($model->getId()) {
            $data = $model->getData();
        }
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Denomination Information')]);

        if ($model->getData('denomination_id')) {
            $fieldset->addField('denomination_id', 'hidden', ['name' => 'denomination_id']);
        }
        $fieldset->addField(
            'denomination_name',
            'text',
            [
                'label' => __('Denomination Name'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'denomination_name',
                'disabled' => false,
            ]
        );
        $fieldset->addField(
            'denomination_value',
            'text',
            [
                'label' => __('Denomination Value'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'denomination_value',
                'disabled' => false,
            ]
        );
        $fieldset->addField(
            'sort_order',
            'text',
            [
                'label' => __('Sort Order'),
                'required' => true,
                'name' => 'sort_order',
                'disabled' => false,
            ]
        );
        $this->_eventManager->dispatch(
            'webpos_denomination_edit_form',
            [
                'form' => $form,
                'field_set' => $fieldset,
                'model_data' => $model
            ]
        );
        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Get Current Model
     *
     * @return mixed
     */
    public function getCurrentModel()
    {
        return $this->_coreRegistry->registry('current_denomination');
    }

    /**
     * Get page title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getPageTitle()
    {
        return $this->getCurrentModel()->getId()
            ? __("Edit Denomination %1", $this->escapeHtml($this->getCurrentModel()->getData('denomination_name')))
            : __('New Denomination');
    }

    /**
     * Get Tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Denomination Information');
    }

    /**
     * Get tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Denomination Information');
    }

    /**
     * Can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
