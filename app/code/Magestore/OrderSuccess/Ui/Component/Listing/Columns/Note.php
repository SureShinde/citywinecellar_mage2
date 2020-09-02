<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Note
 * @package Magestore\OrderSuccess\Ui\Component\Listing\Columns
 */
class Note extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * string
     */
    const NAME = 'note';

    /**
     * string
     */
    const ALT_FIELD = 'name';

    /**
     * string
     */
    protected $storeManager;
    
    /**
     *
     * @var \Magestore\OrderSuccess\Block\Adminhtml\Order\Grid 
     */
    protected $orderGrid;

    /**
     * Note constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\OrderSuccess\Block\Adminhtml\Order\Grid $orderGrid,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->storeManager = $storeManager;
        $this->orderGrid = $orderGrid;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $noteIcon = $this->getAlt($item) ? 'order/note_available.png' : 'order/note.png';
                $noteIconPath = $this->orderGrid->getViewFileUrl('Magestore_OrderSuccess::images/'.$noteIcon);
                $item[$fieldName . '_src'] = $noteIconPath;
                $item[$fieldName . '_orig_src'] = $this->getAlt($item) ?: '';
                $item[$fieldName . '_content'] = $this->getAlt($item) ?: '';
            }
        }

        return $dataSource;
    }

    /**
     * @param array $row
     *
     * @return null|string
     */
    public function getAlt($row)
    {
        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
        return isset($row[$altField]) ? $row[$altField] : null;
    }
}
