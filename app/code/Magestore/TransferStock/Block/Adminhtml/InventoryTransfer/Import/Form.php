<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\TransferStock\Block\Adminhtml\InventoryTransfer\Import;

/**
 * Import form
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Form constructor.
     *
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
            'transferstock/inventoryTransfer/downloadsample',
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
        return 'Please choose a CSV file to  upload product list with a maximum of 1000 SKUs. '
            . 'You can download this sample CSV file.';
    }

    /**
     * Get import urk
     *
     * @return mixed
     */
    public function getImportLink()
    {
        return $this->getUrl(
            'transferstock/inventoryTransfer/import',
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
        return 'Upload Product List';
    }
}
