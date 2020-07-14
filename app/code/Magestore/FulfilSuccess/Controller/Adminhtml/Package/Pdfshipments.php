<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Controller\Adminhtml\Package;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\Order\Pdf\Shipment;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;;
use Magestore\FulfilSuccess\Model\ResourceModel\Package\Grid\CollectionFactory;

class Pdfshipments extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magestore_FulfilSuccess::ready_to_ship_package';

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var ShipmentCollectionFactory
     */
    protected $shipmentCollectionFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * MassPrintShippingLabel constructor.
     * @param Context $context
     * @param Filter $filter
     * @param DateTime $dateTime
     * @param FileFactory $fileFactory
     * @param Shipment $shipment,
     * @param ShipmentCollectionFactory $shipmentCollectionFactory
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        DateTime $dateTime,
        FileFactory $fileFactory,
        Shipment $shipment,
        ShipmentCollectionFactory $shipmentCollectionFactory,
        CollectionFactory $collectionFactory
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->pdfShipment = $shipment;
        $this->collectionFactory = $collectionFactory;
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        parent::__construct($context, $filter);
    }

    /**
     * Batch print shipping labels for whole shipments.
     * Push pdf document with shipping labels to user browser
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface|ResultInterface
     */
    protected function massAction(AbstractCollection $collection)
    {
        if ($collection->getSize()) {
            $shipmentIds = $this->getAllShipmentIds($collection);
            $shipments = $this->shipmentCollectionFactory->create()
                              ->addFieldToFilter('entity_id', ['in' => $shipmentIds]);
            return $this->fileFactory->create(
                sprintf('packingslip%s.pdf', $this->dateTime->date('Y-m-d_H-i-s')),
                $this->pdfShipment->getPdf($shipments)->render(),
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        }
        $this->messageManager->addErrorMessage(__('Can not print package PDF'));
        return $this->resultRedirectFactory->create()->setPath('fulfilsuccess/package/');
    }

    /**
     * @param \Magestore\FulfilSuccess\Model\ResourceModel\Package\Package\Collection $collection
     * @return array
     */
    public function getAllShipmentIds($collection)
    {
        $ids = [];
        foreach ($collection as $package) {
            $ids[] = $package->getShipmentId();
        }
        return $ids;
    }

}