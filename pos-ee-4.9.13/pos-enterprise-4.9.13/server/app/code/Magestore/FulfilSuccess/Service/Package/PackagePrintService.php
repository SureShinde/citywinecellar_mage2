<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Service\Package;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magestore\FulfilSuccess\Service\PrintService;

class PackagePrintService extends PrintService
{
    /**
     * @var \Magento\Shipping\Model\Shipping\LabelGenerator
     */
    protected $labelGenerator;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    public function __construct(
        \Magento\Shipping\Model\Shipping\LabelGenerator $labelGenerator,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->labelGenerator = $labelGenerator;
        $this->_fileFactory = $fileFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * @param \Magento\Sales\Api\Data\ShipmentInterface $shipment
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function printShippingLabel($shipment)
    {
        try {
            $labelContent = $shipment->getShippingLabel();
            if ($labelContent) {
                $pdfContent = null;
                if (stripos($labelContent, '%PDF-') !== false) {
                    $pdfContent = $labelContent;
                } else {
                    $pdf = new \Zend_Pdf();
                    $page = $this->labelGenerator->createPdfPageFromImageString($labelContent);
                    if (!$page) {
                        $this->messageManager->addErrorMessage(
                            __(
                                'We don\'t recognize or support the file extension in this shipment: %1.',
                                $shipment->getIncrementId()
                            )
                        );
                    }
                    $pdf->pages[] = $page;
                    $pdfContent = $pdf->render();
                }

                return $this->_fileFactory->create(
                    'ShippingLabel(' . $shipment->getIncrementId() . ').pdf',
                    $pdfContent,
                    DirectoryList::VAR_DIR,
                    'application/pdf'
                );
            }
        } catch (\Exception $e) {

        }
    }
}