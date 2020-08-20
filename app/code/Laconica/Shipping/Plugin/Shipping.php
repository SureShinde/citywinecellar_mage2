<?php

namespace Laconica\Shipping\Plugin;

class Shipping
{
    protected $logger;
    protected $restrictRepository;
    protected $carrierFactory;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Laconica\Shipping\Model\RestrictRepository $restrictRepository,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory
    )
    {
        $this->logger = $logger;
        $this->restrictRepository = $restrictRepository;
        $this->carrierFactory = $carrierFactory;
    }

    public function aroundCollectCarrierRates(
        \Magento\Shipping\Model\Shipping $subject,
        \Closure $proceed,
        $carrierCode,
        $request
    )
    {
        $carrier = $this->carrierFactory->createIfActive($carrierCode, $request->getStoreId());
        if (!$carrier) {
            return $this;
        }
        $carrier->setActiveFlag('active');
        $result = $carrier->checkAvailableShipCountries($request);
        if (false !== $result && !$result instanceof \Magento\Quote\Model\Quote\Address\RateResult\Error) {
            $result = $carrier->processAdditionalValidation($request);
        }

        if (!$result
            || !$request
            || !$request->getDestRegionId()
            || !$request->getDestPostcode()
            || !$request->getDestCountryId()
            || !$request->getDestCity()
        ) {
            return $proceed($carrierCode, $request);
        }

        $hasRestrictions = $this->restrictRepository->hasRestrictions($carrierCode, $request);
        if ($hasRestrictions) {
            return false;
        }

        return $proceed($carrierCode, $request);
    }
}