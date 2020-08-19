<?php

namespace Laconica\BarcodeSuccess\Block\Product\View;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Theme\Block\Html\Header\Logo;
use Picqer\Barcode\BarcodeGeneratorPNG;

class BarcodePrint extends Template
{
    /**
     * @var bool
     */
    protected $isMultiple = false;

    /**
     * @var array
     */
    protected $barcodes = [];

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Logo
     */
    protected $logo;

    /**
     * ProductPrint constructor.
     * @param Template\Context $context
     * @param CollectionFactory $collectionFactory
     * @param Registry $registry
     * @param Logo $logo
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        Registry $registry,
        Logo $logo,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
        $this->logo = $logo;
        if ($barcodes = $this->_request->getParam('barcodes')) {
            $this->barcodes = $barcodes;
            $this->isMultiple = true;
        }
    }

    /**
     * @param $lookupCode
     * @return string|null
     */
    public function getBarcode($lookupCode)
    {
        if ($lookupCode) {
            $generator = new BarcodeGeneratorPNG();
            return base64_encode($generator->getBarcode($lookupCode, $generator::TYPE_CODE_128));
        }
        return null;
    }

    /**
     * @return array|DataObject[]
     */
    public function getBarcodeProducts()
    {
        $barcodes = $this->_request->getParam('barcodes', []);
        if (count($barcodes) > 0) {
            $barcodes = array_filter($barcodes, 'strlen');
            return $this->collectionFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('item_lookup_code', ['in' => implode(',', $barcodes)])
                ->addAttributeToFilter('type_id', 'simple')
                ->getItems();
        }
        return [];
    }

    /**
     * @return string
     */
    public function getLogoSrc()
    {
        return $this->logo->getLogoSrc();
    }
}
