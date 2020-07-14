<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */
namespace Magestore\ReportSuccess\Ui\DataProvider\Report\StockByLocation\Form\SelectLocations;

use Magento\Ui\Component\Form\Field;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Location
 * @package Magestore\ReportSuccess\Ui\Component\Listing\Columns
 */
class Location extends Field
{
    /**
     * @var \Magestore\ReportSuccess\Api\ReportManagementInterface
     */
    protected $reportManagement;

    /**
     * SelectLocation constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magestore\ReportSuccess\Api\ReportManagementInterface $reportManagement
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magestore\ReportSuccess\Api\ReportManagementInterface $reportManagement,
        array $components = [],
        array $data = []
    )
    {
        $this->reportManagement = $reportManagement;
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
        $data = $this->getData();
        if ($data && $data['config']) {
            if ($this->reportManagement->isMSIEnable()) {
                if ($data['config']['label']) {
                    $data['config']['label'] = __('Select Source');
                }
                if ($data['config']['selectedPlaceholders'] &&
                    $data['config']['selectedPlaceholders']['defaultPlaceholder']
                ) {
                    $data['config']['selectedPlaceholders']['defaultPlaceholder'] = __('All Sources');
                }
                $this->setData($data);
            }
        }
    }
}
