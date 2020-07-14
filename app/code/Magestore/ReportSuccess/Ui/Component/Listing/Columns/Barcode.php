<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class Barcode
 * @package Magestore\ReportSuccess\Ui\Component\Listing\Columns
 */
class Barcode extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magestore\ReportSuccess\Model\Source\Adminhtml\Product\BarcodeAttribute
     */
    protected $barcodeAttribute;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magestore\ReportSuccess\Model\Source\Adminhtml\Product\BarcodeAttribute $barcodeAttribute
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->barcodeAttribute = $barcodeAttribute;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
 * Prepare component configuration
 *
 * @return void
 */
    public function prepare()
    {
        parent::prepare();
        if (!$this->scopeConfig->getValue('reportsuccess/general/enable_barcode_in_report')
            || ($this->scopeConfig->getValue('reportsuccess/general/barcode') == 'sku')){
            $this->_data['config']['componentDisabled'] = true;
        } else {
            $barcodeAttributeArray = $this->barcodeAttribute->toOptionArray();
            foreach ($barcodeAttributeArray as $barcode){
                if ($barcode['value'] == $this->scopeConfig->getValue('reportsuccess/general/barcode')){
                    $this->_data['config']['label'] = $barcode['label'];
                }
            }
        }
    }
}
