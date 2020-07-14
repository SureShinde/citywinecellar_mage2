<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */
namespace Magestore\ReportSuccess\Ui\Component;
/**
 * Class ColumnFactory
 * @package Magestore\ReportSuccess\Ui\Component
 */
class ColumnFactory
{
    /**
     * @var \Magento\Framework\View\Element\UiComponentFactory
     */
    protected $componentFactory;
    /**
     * @var \Magestore\ReportSuccess\Api\ReportManagementInterface
     */
    protected $reportManagement;

    /**
     * @var array
     */
    protected $jsComponentMap = [
        'text' => 'Magento_Ui/js/grid/columns/column',
        'select' => 'Magento_Ui/js/grid/columns/select',
        'multiselect' => 'Magento_Ui/js/grid/columns/select',
        'date' => 'Magento_Ui/js/grid/columns/date',
    ];

    /**
     * @var array
     */
    protected $dataTypeMap = [
        'default' => 'text',
        'text' => 'text',
        'boolean' => 'select',
        'select' => 'select',
        'multiselect' => 'multiselect',
        'date' => 'date',
    ];

    /**
     * @var integer
     */
    protected $sortOrder = 101;

    /**
     * ColumnFactory constructor.
     * @param \Magento\Framework\View\Element\UiComponentFactory $componentFactory
     * @param \Magestore\ReportSuccess\Api\ReportManagementInterface $reportManagement
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponentFactory $componentFactory,
        \Magestore\ReportSuccess\Api\ReportManagementInterface $reportManagement
    )
    {
        $this->componentFactory = $componentFactory;
        $this->reportManagement = $reportManagement;
    }

    /**
     * @param $attribute
     * @param $context
     * @param array $config
     * @return \Magento\Framework\View\Element\UiComponentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create($warehouse, $context, array $config = [])
    {
        $warehouseName = $warehouse->getWarehouseName() ? $warehouse->getWarehouseName(): $warehouse->getName();
        $config = array_merge([
            'label' => __($warehouseName),
            'filter' => 0,
            'controlVisibility' => 0,
            'sortOrder' => $this->sortOrder++,
            'draggable' => false
        ], $config);

        $config['component'] = $this->getJsComponent('text');

        $arguments = [
            'data' => [
                'config' => $config,
            ],
            'context' => $context,
        ];

        return $this->componentFactory->create('loc_' . $warehouse->getId(), 'column', $arguments);
    }

    /**
     * @param string $dataType
     * @return string
     */
    public function getJsComponent($dataType)
    {
        return $this->jsComponentMap[$dataType];
    }
}
