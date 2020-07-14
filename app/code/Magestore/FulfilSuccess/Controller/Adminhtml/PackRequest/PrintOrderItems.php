<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Controller\Adminhtml\PackRequest;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Filesystem\DirectoryList;

class PrintOrderItems extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magestore_FulfilSuccess::pack_request';
    
    /**
     * @var CollectionFactory
     */
    protected $packageCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory
     */
    protected $shipmentCollectionFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\Sales\Model\Order\Pdf\Shipment
     */
    protected $pdfShipment;

    /**
     * PrintOrderItems constructor.
     * @param Context $context
     * @param \Magestore\FulfilSuccess\Model\ResourceModel\Package\Package\CollectionFactory $packageCollectionFactory
     * @param FileFactory $fileFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory
     * @param DateTime $dateTime
     * @param \Magento\Sales\Model\Order\Pdf\Shipment $pdfShipment
     */
    public function __construct(
        Context $context,
        \Magestore\FulfilSuccess\Model\ResourceModel\Package\Package\CollectionFactory $packageCollectionFactory,
        FileFactory $fileFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollectionFactory,
        DateTime $dateTime,
        \Magento\Sales\Model\Order\Pdf\Shipment $pdfShipment
    )
    {
        $this->packageCollectionFactory = $packageCollectionFactory;
        $this->fileFactory = $fileFactory;
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->dateTime = $dateTime;
        $this->pdfShipment = $pdfShipment;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     */
    public function execute()
    {
        $packRequestId = $this->getRequest()->getParam('id');
        /** @var \Magestore\FulfilSuccess\Model\ResourceModel\Package\Package\Collection $packageCollection */
        $packageCollection = $this->packageCollectionFactory->create();
        $package = $packageCollection->addFieldToFilter('pack_request_id', $packRequestId);
        $shipmentIds = $package->getColumnValues('shipment_id');
        /** @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection $shipmentCollection */
        $shipmentCollection = $this->shipmentCollectionFactory->create();
        $shipmentCollection->addFieldToFilter('entity_id', ['in' => $shipmentIds]);
        if ($shipmentCollection->getSize()) {
            return $this->fileFactory->create(
                sprintf('packingslip%s.pdf', $this->dateTime->date('Y-m-d_H-i-s')),
                $this->pdfShipment->getPdf($shipmentCollection)->render(),
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        }
    }
   
}
