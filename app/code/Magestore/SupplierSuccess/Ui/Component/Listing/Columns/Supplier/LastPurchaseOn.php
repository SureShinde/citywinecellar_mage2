<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\SupplierSuccess\Ui\Component\Listing\Columns\Supplier;

use Magento\Framework\Stdlib\BooleanUtils;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\UiComponentInterface;

class LastPurchaseOn extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var BooleanUtils
     */
    private $booleanUtils;

    /**
     * @var \Magento\Framework\App\ProductMetadata
     */
    protected $productMetadata;

    /**
     * LastPurchaseOn constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param TimezoneInterface $timezone
     * @param BooleanUtils $booleanUtils
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        TimezoneInterface $timezone,
        BooleanUtils $booleanUtils,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        array $components = [],
        array $data = []
    ) {
        $this->timezone = $timezone;
        $this->booleanUtils = $booleanUtils;
        $this->moduleManager = $moduleManager;
        $this->productMetadata = $productMetadata;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$this->getData('name')])) {
                    $date = $this->timezone->date(new \DateTime($item[$this->getData('name')]));
                    if (version_compare($this->productMetadata->getVersion(), '2.2.0', '>=')) {
                        $timezone = isset($this->getConfiguration()['timezone'])
                            ? $this->booleanUtils->convert($this->getConfiguration()['timezone'])
                            : true;
                        if (!$timezone) {
                            $date = new \DateTime($item[$this->getData('name')]);
                        }
                    } else {
                        if (isset($this->getConfiguration()['timezone']) && !$this->getConfiguration()['timezone']) {
                            $date = new \DateTime($item[$this->getData('name')]);
                        }
                    }

                    $item[$this->getData('name')] = $date->format('Y-m-d H:i:s');
                }
            }
        }

        return $dataSource;
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        if (!$this->moduleManager->isOutputEnabled('Magestore_PurchaseOrderSuccess'))
            $this->_data['config']['component'] = false;
    }
}
