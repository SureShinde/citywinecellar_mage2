<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\AdjustStock\Block\Adminhtml\AdjustStock\Import;

/**
 * Class Form
 *
 * Import form block
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->setUseContainer(true);
    }

    /**
     * Get adjust stock csv sample link
     *
     * @return mixed
     */
    public function getCsvSampleLink()
    {
        $url = $this->getUrl(
            'adjuststock/adjuststock/downloadsample',
            [
                '_secure' => true,
                'id' => $this->getRequest()->getParam('id')
            ]
        );
        return $url;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return 'Please choose a CSV file to import product adjust stock. You can download this sample CSV file';
    }

    /**
     * Get import urk
     *
     * @return mixed
     */
    public function getImportLink()
    {
        return $this->getUrl(
            'adjuststock/adjuststock/import',
            [
                '_secure' => true,
                'id' => $this->getRequest()->getParam('id')
            ]
        );
    }

    /**
     * Get import title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Import products';
    }
}
