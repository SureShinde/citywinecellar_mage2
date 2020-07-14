<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Block\Adminhtml\Report\Inventory;

use \Magento\Framework\Locale\FormatInterface as LocaleFormat;

/**
 * Class AbstractTotal
 * @package Magestore\ReportSuccess\Block\Adminhtml\Report\Inventory
 */
class AbstractTotal extends \Magento\Backend\Block\Template {

    /**
     * @var LocaleFormat
     */
    protected $localeFormat;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Total constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param LocaleFormat $localeFormat
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        LocaleFormat $localeFormat,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->localeFormat = $localeFormat;
        $this->objectManager = $objectManager;
        $this->storeManager = $context->getStoreManager();
    }

    /**
     * @return array
     */
    public function getPriceFormat() {
        return $this->localeFormat->getPriceFormat(
            null,
            $this->storeManager->getStore()->getBaseCurrency()->getCode()
        );
    }
}