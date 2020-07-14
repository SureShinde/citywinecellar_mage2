<?php

/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */
namespace Magestore\ReportSuccess\Controller\Adminhtml\Report;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magestore\ReportSuccess\Model\Export\AbstractConvertToCsv;

/**
 * Class AbstractExport
 * @package Magestore\ReportSuccess\Controller\Adminhtml\Report
 */
abstract class AbstractExport extends \Magento\Ui\Controller\Adminhtml\Export\GridToCsv
{
    /**
     * @var
     */
    protected $customConverter;
    /**
     * @var string
     */
    protected $fileName = '';
    /**
     * @var string
     */
    protected $converterClassName = '';
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * AbstractExport constructor.
     * @param Context $context
     * @param AbstractConvertToCsv $converter
     * @param FileFactory $fileFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    public function __construct(
        Context $context,
        AbstractConvertToCsv $converter,
        FileFactory $fileFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    )
    {
        parent::__construct($context, $converter, $fileFactory);
        $this->localeDate = $localeDate;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Exception
     */
    public function execute()
    {
        return $this->fileFactory->create($this->fileName . $this->localeDate->date()->format('YmdHis') . '.csv', $this->getConverter()->getCsvFile(), 'var');
    }

    /**
     * @return mixed
     */
    public function getConverter() {
        if(!$this->customConverter) {
            $this->customConverter = $this->_objectManager->get($this->converterClassName);
        }
        return $this->customConverter;
    }
}