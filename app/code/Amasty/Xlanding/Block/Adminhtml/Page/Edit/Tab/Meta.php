<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Block\Adminhtml\Page\Edit\Tab;

class Meta extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    const META_TITLE = 'meta_title_';
    const META_KEYWORDS = 'meta_keywords_';
    const META_DESCRIPTION = 'meta_description_';

    /**
     * @var \Magento\Store\Model\System\Store
     */
    private $systemStore;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Amasty_Xlanding::page')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $model = $this->_coreRegistry->registry('amasty_xlanding_page');
        $metaData = $model->getMetaData();

        foreach ($this->systemStore->getStoreCollection() as $store) {
            $fieldset = $form->addFieldset(
                'meta_fieldset' . $store->getId(),
                ['legend' => $store->getName(), 'class' => 'fieldset-wide']
            );

            $fieldset->addField(
                self::META_TITLE . $store->getId(),
                'text',
                [
                    'name' => self::META_TITLE . $store->getId(),
                    'label' => __('Title'),
                    'title' => __('Meta Title'),
                    'disabled' => $isElementDisabled
                ]
            );

            $fieldset->addField(
                self::META_KEYWORDS . $store->getId(),
                'text',
                [
                    'name' => self::META_KEYWORDS . $store->getId(),
                    'label' => __('Keywords'),
                    'title' => __('Meta Keywords'),
                    'disabled' => $isElementDisabled
                ]
            );

            $fieldset->addField(
                self::META_DESCRIPTION . $store->getId(),
                'textarea',
                [
                    'name' => self::META_DESCRIPTION . $store->getId(),
                    'label' => __('Description'),
                    'title' => __('Meta Description'),
                    'disabled' => $isElementDisabled
                ]
            );

            if (isset($metaData[$store->getId()])) {
                $this->setMetaData($model, self::META_TITLE . $store->getId(), $metaData[$store->getId()]);
                $this->setMetaData($model, self::META_KEYWORDS . $store->getId(), $metaData[$store->getId()]);
                $this->setMetaData($model, self::META_DESCRIPTION . $store->getId(), $metaData[$store->getId()]);
            }
        }

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param $model
     * @param string $field
     * @param array $metaData
     */
    private function setMetaData($model, $field, $metaData)
    {
        if (isset($metaData[$field])) {
            $model->setData($field, $metaData[$field]);
        }
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Meta Data');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Meta Data');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
