<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Controller\Adminhtml\Reports\Export;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use Magestore\PosReports\Model\Export\ConvertToCsv;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

/**
 * Class ReportToCsv
 *
 * Used to create Report To Csv
 */
class ReportToCsv extends \Magestore\PosReports\Controller\Adminhtml\AbstractAction implements HttpGetActionInterface
{
    /**
     * @var ConvertToCsv
     */
    protected $converter;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ReportToCsv constructor.
     *
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ConvertToCsv $converter
     * @param FileFactory $fileFactory
     * @param Filter|null $filter
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        ConvertToCsv $converter,
        FileFactory $fileFactory,
        Filter $filter = null,
        LoggerInterface $logger = null
    ) {
        parent::__construct($context, $registry);
        $this->converter = $converter;
        $this->fileFactory = $fileFactory;
        $this->filter = $filter ?: ObjectManager::getInstance()->get(Filter::class);
        $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
    }

    /**
     * Export data provider to CSV
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $exportFileName = 'export.csv';
        $component = $this->getComponent();
        if ($component && $this->converter->getMetaDataProvider()->isPosReport($component)) {
            $report = $component->getReport();
            $exportFileName = $report->getExportCsvFileName();
        }
        return $this->fileFactory->create($exportFileName, $this->converter->getCsvFile(), 'var');
    }

    /**
     * Get Component
     *
     * @return \Magento\Framework\View\Element\UiComponentInterface|null
     */
    public function getComponent()
    {
        $component = null;
        if ($this->_request->getParam('namespace')) {
            try {
                $component = $this->filter->getComponent();
            } catch (\Throwable $exception) {
                $this->logger->critical($exception);
            }
        }
        return $component;
    }

    /**
     * Checking if the user has access to requested component.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        $component = $this->getComponent();
        if ($component) {
            $dataProviderConfig = $component->getContext()
                ->getDataProvider()
                ->getConfigData();
            if (isset($dataProviderConfig['aclResource'])) {
                return $this->_authorization->isAllowed(
                    $dataProviderConfig['aclResource']
                );
            }
            return true;
        } else {
            return false;
        }
    }
}
