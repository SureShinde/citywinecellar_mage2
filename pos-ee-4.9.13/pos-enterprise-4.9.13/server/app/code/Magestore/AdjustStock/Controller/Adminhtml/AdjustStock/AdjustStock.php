<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Controller\Adminhtml\AdjustStock;

/**
 * Class AdjustStock
 * @package Magestore\AdjustStock\Controller\Adminhtml\AdjustStock
 */
abstract class AdjustStock extends \Magestore\AdjustStock\Controller\Adminhtml\AbstractAction
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $adminSession;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * @var \Magento\Framework\Filesystem\File\WriteFactory
     */
    protected $fileWriteFactory;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $driverFile;

    /**
     * @var \Magestore\AdjustStock\Model\AdjustStockFactory
     */
    protected $adjustStockFactory;

    /**
     * @var \Magestore\AdjustStock\Model\ResourceModel\AdjustStock
     */
    protected $adjustStockResource;
    /**
     * @var \Magestore\AdjustStock\Api\AdjustStock\AdjustStockManagementInterface
     */
    protected $adjustStockManagement;


    /**
     * AdjustStock constructor.
     * @param \Magestore\AdjustStock\Controller\Adminhtml\Context $context
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Backend\Model\Auth\Session $adminSession
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param \Magento\Framework\Filesystem\File\WriteFactory $fileWriteFactory
     * @param \Magento\Framework\Filesystem\Driver\File $driverFile
     * @param \Magestore\AdjustStock\Model\AdjustStockFactory $adjustStockFactory
     * @param \Magestore\AdjustStock\Model\ResourceModel\AdjustStock $adjustStockResource
     * @param \Magestore\AdjustStock\Api\AdjustStock\AdjustStockManagementInterface $adjustStockManagement
     */
    public function __construct(
        \Magestore\AdjustStock\Controller\Adminhtml\Context $context,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\Filesystem\File\WriteFactory $fileWriteFactory,
        \Magento\Framework\Filesystem\Driver\File $driverFile,
        \Magestore\AdjustStock\Model\AdjustStockFactory $adjustStockFactory,
        \Magestore\AdjustStock\Model\ResourceModel\AdjustStock $adjustStockResource,
        \Magestore\AdjustStock\Api\AdjustStock\AdjustStockManagementInterface $adjustStockManagement
    ){
        parent::__construct($context);
        $this->moduleManager = $moduleManager;
        $this->adminSession = $adminSession;
        $this->filesystem = $filesystem;
        $this->fileFactory = $fileFactory;
        $this->csvProcessor = $csvProcessor;
        $this->fileWriteFactory = $fileWriteFactory;
        $this->driverFile = $driverFile;
        $this->adjustStockFactory = $adjustStockFactory;
        $this->adjustStockResource = $adjustStockResource;
        $this->adjustStockManagement = $adjustStockManagement;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_AdjustStock::adjuststock');
    }
}
