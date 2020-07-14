<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Block\Adminhtml\Banners\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Stdlib\DateTime;

class General extends Generic implements TabInterface
{
    protected $_activeSource;
    protected $_systemStore;
    protected $_groupRepository;
    protected $_searchCriteriaBuilder;
    protected $_objectConverter;
    protected $_datetime;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Amasty\PromoBanners\Model\Source\Active $activeSource,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Convert\DataObject $objectConverter,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_activeSource = $activeSource;
        $this->_systemStore = $systemStore;
        $this->_groupRepository = $groupRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_objectConverter = $objectConverter;
        $this->_datetime = $datetime;
    }

    protected $_statuses;

    public function getTabLabel()
    {
        return __('General');
    }

    public function getTabTitle()
    {
        return __('General');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_amasty_promo_banner');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('ampromobanners_');
        $fieldset = $form->addFieldset('general_fieldset', ['legend' => __('General')]);
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'rule_name',
            'text',
            [
                'name'     => 'rule_name',
                'label'    => __('Name'),
                'title'    => __('Name'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'label'    => __('Status'),
                'title'    => __('Status'),
                'name'     => 'is_active',
                'required' => true,
                'options'  => $this->_activeSource->toOptionArray(),
            ]
        );

        $fieldset->addField(
            'stores',
            'multiselect',
            [
                'label'  => __('Stores'),
                'name'   => 'stores[]',
                'values' => $this->_systemStore->getStoreValuesForForm(),
                'note'   => __('Leave empty to show the banner in all stores'),
            ]
        );

        $customerGroups = $this->_groupRepository->getList($this->_searchCriteriaBuilder->create())->getItems();

        $fieldset->addField(
            'cust_groups',
            'multiselect',
            [
                'name'   => 'cust_groups[]',
                'label'  => __('Customer Groups'),
                'values' => $this->_objectConverter->toOptionArray($customerGroups, 'id', 'code'),
                'note'   => __('Leave empty to show the banner for all groups'),
            ]
        );

        $fieldset->addField(
            'from_date',
            'date',
            [
                'name'        => 'from_date',
                'label'       => __('From Date'),
                'title'       => __('From Date'),
                'date_format' => DateTime::DATE_INTERNAL_FORMAT,
                'time_format' => 'HH:mm:ss'
            ]
        );

        $fieldset->addField(
            'to_date',
            'date',
            [
                'name'        => 'to_date',
                'label'       => __('To Date'),
                'title'       => __('To Date'),
                'date_format' => DateTime::DATE_INTERNAL_FORMAT,
                'time_format' => 'HH:mm:ss'
            ]
        );

        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name'  => 'sort_order',
                'class' => 'validate-number validate-zero-or-greater',
                'label' => __('Priority'),
            ]
        );

        $data = $model->getData();

        if (isset($data['from_date'])) {
            $data['from_date'] = $this->_datetime->date(DateTime::DATETIME_PHP_FORMAT, strtotime($data['from_date']));
        }

        if (isset($data['to_date'])) {
            $data['to_date'] = $this->_datetime->date(DateTime::DATETIME_PHP_FORMAT, strtotime($data['to_date']));
        }

        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
