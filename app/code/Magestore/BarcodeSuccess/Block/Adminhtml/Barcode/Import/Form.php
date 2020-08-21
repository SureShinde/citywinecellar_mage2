<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Block\Adminhtml\Barcode\Import;

/**
 * Class Form
 *
 * Used to create form
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\ProductVideo\Helper\Media
     */
    protected $mediaHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * Form constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\ProductVideo\Helper\Media $mediaHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\ProductVideo\Helper\Media $mediaHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->mediaHelper = $mediaHelper;
        $this->urlBuilder = $context->getUrlBuilder();
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * Prepare form
     *
     * @return \Magento\Backend\Block\Widget\Form\Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {

        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/importdata'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );
        $fieldset = $form->addFieldset('profile_fieldset', []);
        $fieldset->addField(
            'reason',
            'textarea',
            [
                'label' => __('Reason'),
                'title' => __('Reason'),
                'name' => 'reason',
                'required' => true,
            ]
        );
        $fieldset->addField(
            'file_csv',
            'file',
            [
                'label' => __('Import File'),
                'title' => __('Import File'),
                'name' => 'file_csv',
                'required' => true,
                'note' => __('Please select a CSV file')
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
