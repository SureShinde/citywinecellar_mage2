<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Block\Adminhtml\Banners\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Amasty\PromoBanners\Helper\Data as PromoBannersHelper;

class Segments extends Generic implements TabInterface
{
    /**
     * @var \Amasty\PromoBanners\Model\Factories\SegmentFactory
     */
    private $segmentFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Amasty\PromoBanners\Model\Factories\SegmentFactory $segmentFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->segmentFactory = $segmentFactory;
        $this->coreRegistry = $coreRegistry;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Segments');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Segments');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->moduleManager->isEnabled(PromoBannersHelper::AMASTY_SEGMENT_MODULE_DEPEND_NAMESPACE)) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return mixed
     */
    public function getRule()
    {
        return $this->coreRegistry->registry('current_amasty_promo_banner');
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->getRule();

        $options = [];
        if ($this->moduleManager->isEnabled(PromoBannersHelper::AMASTY_SEGMENT_MODULE_DEPEND_NAMESPACE)) {
            $options = $this->segmentFactory->getSegmentCollection()->addActiveFilter()->toOptionArray();
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('amasty_banner_');

        $fldInfo = $form->addFieldset('segments_fieldset', ['legend' => __('Segments')]);

        $fldInfo->addField(
            'segments_ids',
            'multiselect',
            [
                'name'   => 'segments_ids[]',
                'label'  => $this->getTabLabel(),
                'title'  => $this->getTabTitle(),
                'values' => $options
            ]
        );

        $values = $model->getData();

        if (isset($values['segments']) && !empty($values['segments'])) {
            $values['segments_ids'] = explode(',', $values['segments']);
        }

        $form->setValues($values);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
