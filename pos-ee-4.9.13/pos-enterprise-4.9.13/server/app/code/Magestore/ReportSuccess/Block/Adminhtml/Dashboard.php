<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Block\Adminhtml;
/**
 * Class Dashboard
 * @package Magestore\ReportSuccess\Block\Adminhtml
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
     * Dashboard constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    )
    {
        parent::__construct($context, $objectManager, $messageManager, $authorization, $moduleManager, $data);
    }

    /**
     * check Starter package has been installed (has Multi-stock feature): StockManagementSuccess installed
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
     * check PurchaseOrderSuccess installed
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
     * get a list of staff report controllers and names
     *
     * @return array
     */
    public function getInventoryReportList()
    {
        $reports = array(
            'stockValue' => ['title' => __('Stock Value'), 'description' => __('View current stock levels, avg. cost and total stock value.')],
            'stockDetails' => ['title' => __('Stock Details'), 'description' => __('View Qty. on-hand, Qty. Available, Qty. to ship and Qty. on order')],
            'stockByLocation' => ['title' => __('Stock by Warehouse'), 'description' => __('Compare stock levels between warehouse')],
            'incomingStock' => ['title' => __('Incoming Stock'), 'description' => __('View PO list of incoming stock and their cost')],
            'historicalStock' => ['title' => __('Historical Stock'), 'description' => __('Export stock levels, avg.cost and stock value from a past date.')]
        );
        if ($this->checkIsStarterPackage()
            || !$this->isEnableModule("Magestore_PurchaseOrderSuccess")) {
            unset($reports['stockByLocation']);
        }
        if (!$this->checkPurchaseManagementModuleInstalled()) {
            unset($reports['incomingStock']);
        }
        return $reports;
    }

    /**
     * get a list of location report controllers and names
     *
     * @return array
     */
    public function getSalesReports()
    {
        return array(
            'salesByProduct' => ['title' => __('Product'), 'description' => __('View sales, COGS and profit statistics by product.')],
            'salesByLocation' => ['title' => __('Warehouse'), 'description' => __('View sales, COGS and profit statistics by warehouse.')],
            'salesByShippingMethod' => ['title' => __('Shipping Method'), 'description' => __('View sales, COGS and profit statistics by shipping method.')],
            'salesByPaymentMethod' => ['title' => __('Payment Method'), 'description' => __('View sales, COGS and profit statistics by payment method.')],
            'salesByOrderStatus' => ['title' => __('Order Status'), 'description' => __('View sales, COGS and profit statistics by order status.')],
            'salesByCustomer' => ['title' => __('Customer'), 'description' => __('View sales, COGS and profit statistics by customer.')]
        );
    }

    /**
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
     * @param $permission
     * @return bool
     */
    public function isAllowed($permission)
    {
        return $this->_authorization->isAllowed($permission);
    }

    /**
     * get report link from name
     *
     * @param string
     * @return string
     */
    public function getReportLink($controller, $group = 'inventory')
    {
        $path = 'omcreports/' . $group . '/' . $controller;
        return $this->getUrl($path, array('_forced_secure' => $this->getRequest()->isSecure()));
    }

    /**
     * get current report name
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
        if (isset($reportList[$controller]))
            $reportName = $reportList[$controller];
        return $reportName;
    }

    /**
     * @param $module
     * @return bool
     */
    public function isEnableModule($module)
    {
        return $this->_moduleManager->isEnabled($module);
    }
}
