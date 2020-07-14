<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Controller\Adminhtml\Inventory;

use Magento\Framework\Controller\ResultFactory;
use Magestore\ReportSuccess\Ui\DataProvider\Report\StockByLocation\DataProvider;

/**
 * Class SaveStockByLocation
 * @package Magestore\ReportSuccess\Controller\Adminhtml\Inventory
 */
class SaveStockByLocation extends \Magento\Backend\App\Action
{
    /**
     * @var \Magestore\ReportSuccess\Model\Bookmark
     */
    protected $bookmark;
    
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magestore\ReportSuccess\Model\Bookmark $bookmark
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magestore\ReportSuccess\Model\Bookmark $bookmark
    ) {
        $this->bookmark = $bookmark;
        parent::__construct($context);
    }
    
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        try {
            $this->bookmark->setLocations($this->_request->getParam('location'));
            $this->bookmark->setMetric($this->_request->getParam('metric'));
            $this->bookmark->saveBookmark();
        } catch (\Exception $e) {
            // Ignore save bookmark error
        }
        return $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
    }
}
