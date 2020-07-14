<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Block\Adminhtml\Page\Edit\Tab;

use Amasty\Xlanding\Api\Data\PageInterface;
use \Magento\Backend\Block\Widget\Form\Element\Dependence;
use \Amasty\Xlanding\Model\Page;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Exception\NoSuchEntityException;

class Main extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Amasty\Xlanding\Model\Source\Category
     */
    private $categoryModel;

    /**
     * @var \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory
     */
    private $dependencyFieldFactory;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Amasty\Xlanding\Model\Source\Category $categoryModel,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $dependencyFieldFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_systemStore = $systemStore;
        $this->categoryModel = $categoryModel;
        $this->categoryRepository = $categoryRepository;
        $this->dependencyFieldFactory = $dependencyFieldFactory;
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Amasty\Xlanding\Model\Page */
        $model = $this->_coreRegistry->registry('amasty_xlanding_page');

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

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Page Information')]);

        if ($model->getId()) {
            $fieldset->addField('page_id', 'hidden', ['name' => 'page_id']);
        }

        $statusField = $this->prepareStatusField($fieldset, $isElementDisabled);

        $categoryField = $fieldset->addField(
            PageInterface::DYNAMIC_CATEGORY_ID,
            'select',
            [
                'label' => __('Linked Dynamic Category'),
                'title' => __('Linked Dynamic Category'),
                'name' => PageInterface::DYNAMIC_CATEGORY_ID,
                'required' => false,
                'options' => $this->categoryModel->toArray(),
                'disabled' => $isElementDisabled
            ]
        );

        $linkField = $fieldset->addField(
            'category_link',
            'link',
            [
                'label' => __('Dynamic Category Edit Page Link'),
                'title' => __('Dynamic Category Edit Page Link'),
                'href' => $this->getUrl('catalog/category/edit', ['id' => $model->getDynamicCategoryId()])
            ]
        );


        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Page Title'),
                'title' => __('Page Title'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $urlKeyField = $fieldset->addField(
            'identifier',
            'text',
            [
                'name' => 'identifier',
                'label' => __('URL Key'),
                'title' => __('URL Key'),
                'class' => 'validate-identifier',
                'required' => true,
                'note' => __('Relative to Web Site Base URL'),
                'disabled' => $isElementDisabled
            ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $storeField = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                \Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element::class
            );
            $storeField->setRenderer($renderer);
        } else {
            $storeField = $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }


        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        $form->setValues($model->getData());
        $linkValue = __('Link');
        if ($categoryId = $model->getDynamicCategoryId()) {
            try {
                $category = $this->categoryRepository->get($categoryId);
                $linkValue = $category->getName();
            } catch (NoSuchEntityException $e) {
                //do nothing
            }
        }
        $form->addValues(['category_link' => $linkValue]);
        $this->setForm($form);

        /** @var Dependence */
        $dependence = $this->getLayout()->createBlock(Dependence::class);
        $dependence
            ->addFieldMap($statusField->getHtmlId(), $statusField->getName())
            ->addFieldMap($linkField->getHtmlId(), $linkField->getName())
            ->addFieldMap($categoryField->getHtmlId(), $categoryField->getName())
            ->addFieldMap($urlKeyField->getHtmlId(), $urlKeyField->getName())
            ->addFieldDependence(
                $categoryField->getName(),
                $statusField->getName(),
                Page::STATUS_DYNAMIC
            )->addFieldDependence(
                $linkField->getName(),
                $statusField->getName(),
                Page::STATUS_DYNAMIC
            );

        $this->setChild(
            'form_after',
            $dependence
        );
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Page Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Page Information');
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

    /**
     * @param Fieldset $fieldset
     * @param $isElementDisabled
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function prepareStatusField(Fieldset $fieldset, $isElementDisabled)
    {
        $model = $this->_coreRegistry->registry('amasty_xlanding_page');
        $statusField = $fieldset->addField(
            PageInterface::LANDING_IS_ACTIVE,
            'select',
            [
                'label' => __('Status'),
                'title' => __('Page Status'),
                'name' => PageInterface::LANDING_IS_ACTIVE,
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled,
                'note'  => __(
                    "Select 'Dynamic Category' to use Landing Page conditions for category product display.<br/>"
                    . "The selected Landing Page becomes unavailable by direct link."
                )
            ]
        );

        $html = '<script type="text/javascript">
            require(["jquery"], function ($) {
                var element = $("#' . $statusField->getHtmlId() . '");
                element.on("change", function () {
                    $("#page_tabs_design_section").toggle(element.val() != "' . Page::STATUS_DYNAMIC . '");
                    $("#page_tabs_meta_section").toggle(element.val() != "' . Page::STATUS_DYNAMIC . '");
                });
                $("#page_tabs_design_section").toggle(element.val() != "' . Page::STATUS_DYNAMIC . '");
                $("#page_tabs_meta_section").toggle(element.val() != "' . Page::STATUS_DYNAMIC . '");
            });
            </script>';
        $statusField->setData('after_element_html', $html);

        return $statusField;
    }
}
