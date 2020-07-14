<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\PurchaseOrderSuccess\Ui\Controller;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magestore\PurchaseOrderSuccess\Model\Export\ConvertToXml;

/**
 * Class Render
 */
class GridToXml extends Action
{
    /**
     * @var ConvertToXml
     */
    protected $converter;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @param Context $context
     * @param ConvertToXml $converter
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        ConvertToXml $converter,
        FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->converter = $converter;
        $this->fileFactory = $fileFactory;
    }

    /**
     * Export data provider to XML
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $name = $this->converter->getFileName().'.xml';
        if (strpos($name, 'os_') !== false) {
            return $this->fileFactory->create($name, $this->converter->getXmlFile(), 'var');
        }
        return $this->fileFactory->create('export.xml', $this->converter->getXmlFile(), 'var');
    }
}
