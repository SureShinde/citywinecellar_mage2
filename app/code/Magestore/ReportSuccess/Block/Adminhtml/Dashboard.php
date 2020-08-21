<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Block\Adminhtml;

/**
 * Class Dashboard
 *
 * Used to create Dashboard
 */
class Dashboard extends \Magestore\ReportSuccess\Block\Adminhtml\AbstractBlock
{
    /**
     * report list
     *
     * @var array
     */
    protected $_reportList;

    /**
     * Check Starter package has been installed (has Multi-stock feature): StockManagementSuccess installed
     *
     * @return bool
     */
    public function checkIsStarterPackage()
    {
        if ($this->_moduleManager->isEnabled('Magestore_StockManagementSuccess')) {
            return true;
        }
        return false;
    }

    /**
     * Check PurchaseOrderSuccess installed
     *
     * @return bool
     */
    public function checkPurchaseManagementModuleInstalled()
    {
        if ($this->_moduleManager->isEnabled('Magestore_PurchaseOrderSuccess')) {
            return true;
        }
        return false;
    }

    /**
     * Get a list of staff report controllers and names
     *
     * @return array
     */
    public function getInventoryReportList()
    {
        $reports = [
            'stockValue' => [
                'title' => __('Stock Value'),
                'description' => __('View current stock levels, avg. cost and total stock value.')
            ],
            'stockDetails' => [
                'title' => __('Stock Details'),
                'description' => __('View Qty. on-hand, Qty. Available, Qty. to ship and Qty. on order')
            ],
            'stockByLocation' => [
                'title' => __('Stock by Source'),
                'description' => __('Compare stock levels between source')
            ],
            'incomingStock' => [
                'title' => __('Incoming Stock'),
                'description' => __('View PO list of incoming stock and their cost')
            ],
            'historicalStock' => [
                'title' => __('Historical Stock'),
                'description' => __('Export stock levels, avg.cost and stock value from a past date.')
            ]
        ];
        /**
         * @see \Magestore\ReportSuccess\Model\Report\PanelItems\StockByLocation::modifyVisible()
         * */
        if ($this->checkIsStarterPackage()
            || !$this->isEnableModule("Magestore_PurchaseOrderSuccess")) {
            unset($reports['stockByLocation']);
        }

        /**
         * @see \Magestore\ReportSuccess\Model\Report\PanelItems\IncomingStock::modifyVisible()
         * */
        if (!$this->checkPurchaseManagementModuleInstalled()) {
            unset($reports['incomingStock']);
        }
        return $reports;
    }

    /**
     * Get a list of location report controllers and names
     *
     * @return array
     */
    public function getSalesReports()
    {
        return [
            'salesByProduct' => [
                'title' => __('Product'),
                'description' => __('View sales, COGS and profit statistics by product.')
            ],
            'salesByLocation' => [
                'title' => __('Warehouse'),
                'description' => __('View sales, COGS and profit statistics by warehouse.')
            ],
            'salesByShippingMethod' => [
                'title' => __('Shipping Method'),
                'description' => __('View sales, COGS and profit statistics by shipping method.')
            ],
            'salesByPaymentMethod' => [
                'title' => __('Payment Method'),
                'description' => __('View sales, COGS and profit statistics by payment method.')
            ],
            'salesByOrderStatus' => [
                'title' => __('Order Status'),
                'description' => __('View sales, COGS and profit statistics by order status.')
            ],
            'salesByCustomer' => [
                'title' => __('Customer'),
                'description' => __('View sales, COGS and profit statistics by customer.')
            ]
        ];
    }

    /**
     * Get Report List
     *
     * @return array
     */
    public function getReportList()
    {
        if (!$this->_reportList) {
            $this->_reportList = array_merge(
                $this->getInventoryReportList(),
                $this->getSalesReports()
            );
        }
        return $this->_reportList;
    }

    /**
     * Is Allowed
     *
     * @param string $permission
     * @return bool
     */
    public function isAllowed($permission)
    {
        return $this->_authorization->isAllowed($permission);
    }

    /**
     * Get report link from give path
     *
     * @param string $path
     * @return string
     * */
    public function getActionUrl($path)
    {
        return $this->getUrl($path, ['_forced_secure' => $this->getRequest()->isSecure()]);
    }

    /**
     * Get report link from name
     *
     * @param string $controller
     * @param string $group
     * @return string
     */
    public function getReportLink($controller, $group = 'inventory')
    {
        $path = 'omcreports/' . $group . '/' . $controller;
        return $this->getUrl($path, ['_forced_secure' => $this->getRequest()->isSecure()]);
    }

    /**
     * Get current report name
     *
     * @param
     * @return string
     */
    public function getCurrentReportName()
    {
        $controller = $this->getRequest()->getControllerName();
        $controller = str_replace('report_', '', $controller);
        $reportList = $this->getReportList();
        $reportName = '';
        if (isset($reportList[$controller])) {
            $reportName = $reportList[$controller];
        }
        return $reportName;
    }

    /**
     * Is Enable Module
     *
     * @param string $module
     * @return bool
     */
    public function isEnableModule($module)
    {
        return $this->_moduleManager->isEnabled($module);
    }
}
