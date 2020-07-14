<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilReport\Block\Adminhtml\Report\Filter;

class Form extends \Magento\Reports\Block\Adminhtml\Filter\Form
{
    /**
     * @var string
     */
    protected $_newFieldFilterCode;

    /**
     * @var string
     */
    protected $_newFieldFilterName;

    /**
     * @var array
     */
    protected $_newFieldFilterOption;

    /**
     * @var \Magestore\FulfilReport\Model\Action\Options
     */
    protected $actionOptions;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magestore\FulfilReport\Model\Action\Options $actionOptions,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->actionOptions = $actionOptions;
    }

    /**
     * Add fieldset with general report fields
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $actionUrl = $this->getUrl('*/*/sales');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'filter_form',
                    'action' => $actionUrl,
                    'method' => 'get'
                ]
            ]
        );

        $htmlIdPrefix = 'sales_report_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $fieldSet = $form->addFieldset('base_fieldset', ['legend' => __('Filter')]);

        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);

        $fieldSet->addField('store_ids', 'hidden', ['name' => 'store_ids']);

        if ($this->_newFieldFilterCode)
            $fieldSet->addField(
                $this->_newFieldFilterCode,
                'select',
                [
                    'name' => $this->_newFieldFilterCode,
                    'label' => __($this->_newFieldFilterName),
                    'options' => $this->_newFieldFilterOption,
                ],
                'to'
            );

        $fieldSet->addField(
            'from',
            'date',
            [
                'name' => 'from',
                'date_format' => $dateFormat,
                'label' => __('From'),
                'title' => __('From'),
                'required' => true
            ]
        );

        $fieldSet->addField(
            'to',
            'date',
            [
                'name' => 'to',
                'date_format' => $dateFormat,
                'label' => __('To'),
                'title' => __('To'),
                'required' => true
            ]
        );

        if ($this->getFulfilAction()) {
            $fieldSet->addField(
                'fulfil_action',
                'select',
                [
                    'name' => 'fulfil_action',
                    'label' => __('Fulfil Action'),
                    'options' => $this->actionOptions->toOptionArray(),
                ],
                'to'
            );
        }

        $form->setUseContainer(true);
        $this->setForm($form);

        return $this;
    }

}
