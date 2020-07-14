<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\Package;

class PackageDetail extends \Magento\Shipping\Block\Adminhtml\Order\Packaging
{
    /**
     * @var \Magento\Sales\Api\ShipmentRepositoryInterface
     */
    protected $shipmentRepository;

    /**
     * @var \Magestore\FulfilSuccess\Api\PackageRepositoryInterface
     */
    protected $packageRepository;

    protected $shippingHelper;

    protected $trackFactory;

    protected $trackResource;

    /**
     * PackageDetail constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Shipping\Model\Carrier\Source\GenericInterface $sourceSizeModel
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Shipping\Model\CarrierFactory $carrierFactory
     * @param \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository
     * @param \Magestore\FulfilSuccess\Api\PackageRepositoryInterface $packageRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Shipping\Model\Carrier\Source\GenericInterface $sourceSizeModel,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory,
        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository,
        \Magestore\FulfilSuccess\Api\PackageRepositoryInterface $packageRepository,
        \Magento\Shipping\Helper\Data $shippingHelper,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\Track $trackResource,
        array $data = [])
    {
        parent::__construct($context, $jsonEncoder, $sourceSizeModel, $coreRegistry, $carrierFactory, $data);
        $this->shipmentRepository = $shipmentRepository;
        $this->packageRepository = $packageRepository;
        $this->shippingHelper = $shippingHelper;
        $this->trackFactory = $trackFactory;
        $this->trackResource = $trackResource;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('Magestore_FulfilSuccess::package/package_detail.phtml');
    }

    /**
     * Get package info
     *
     * @return \Magestore\FulfilSuccess\Api\Data\PackageInterface
     */
    public function getPackage()
    {
        $packageId = $this->getData('package_id');
        $package = $this->packageRepository->get($packageId);
        return $package;
    }

    /**
     * Retrieve shipment model instance
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getShipment()
    {
        $package = $this->getPackage();
        $shipmentId = $package->getShipmentId();
        $shipment = $this->shipmentRepository->get($shipmentId);

        return $shipment;
    }

    /**
     * @param $trackId
     * @return string
     */
    public function getTrackById($trackId)
    {
        $track = $this->trackFactory->create();
        $this->trackResource->load($track, $trackId);
        return $track;
    }

    /**
     * @param $trackId
     * @return string
     */
    public function getTrackUrl($trackId)
    {
        $track = $this->getTrackById($trackId);
        $url = $this->shippingHelper->getTrackingPopupUrlBySalesModel($track);
        return $url;
    }
}