<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Block\Adminhtml\Barcode\Import;
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

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


    protected function _prepareForm()
    {

        $form = $this->_formFactory->create(['data' => array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/importdata'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        )]);
        $fieldset = $form->addFieldset('profile_fieldset', array());
        $fieldset->addField('reason', 'textarea', array(
            'label' => __('Reason'),
            'title' => __('Reason'),
            'name' => 'reason',
            'required' => true,
        ));
        $fieldset->addField('file_csv', 'file', array(
            'label' => __('Import File'),
            'title' => __('Import File'),
            'name' => 'file_csv',
            'required' => true,
            'note' => __('Please select a CSV file')
        ));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}