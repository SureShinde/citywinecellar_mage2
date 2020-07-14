<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Block\Adminhtml\Template;

class Description extends \Magestore\BarcodeSuccess\Block\Barcode\Container
{
    /**
     * Description constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magestore\BarcodeSuccess\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magestore\BarcodeSuccess\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $helper, $data);
        $this->setTemplate('Magestore_BarcodeSuccess::template/description.phtml');
    }

    /**
     * 
     * @return string
     */
    public function getDescriptionImage()
    {
        return $this->getViewFileUrl('Magestore_BarcodeSuccess::image/description.png');
    }
}
