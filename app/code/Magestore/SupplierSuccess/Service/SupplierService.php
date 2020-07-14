<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Service;

class SupplierService extends AbstractService
{
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

    /**
     * @return array
     */
    public function getStatusOptions()
    {
        return [
            self::STATUS_ENABLE => __('Enable'),
            self::STATUS_DISABLE => __('Disable')
        ];
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toStatusOptionArray()
    {
        $availableOptions = $this->getStatusOptions();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }

    /**
     * @param $supplierCode
     * @return \Magestore\SupplierSuccess\Api\Data\SupplierInterface
     */
    public function getSupplierBySupplierCode($supplierCode)
    {
        return $this->supplierRepositoryInterface->getByCode($supplierCode);
    }

    /**
     * @param $productIds
     * @return \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Collection
     */
    public function getSupplierByProductId($productIds)
    {
        /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\Collection $supplierProductCollection */
        $supplierProductCollection = $this->_supplierProductCollectionFactory->create()
            ->addFieldToFilter('product_id', ['in' => $productIds]);
        $supplierIds = $supplierProductCollection->getColumnValues('supplier_id');
        /** @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Collection $supplierCollection */
        $supplierCollection = $this->supplierCollectionFactory->create()
            ->addFieldToFilter('status', self::STATUS_ENABLE)
            ->addFieldToFilter('supplier_id', ['in' => $supplierIds]);
        return $supplierCollection;
    }

    public function setPasswordSupplier($newPassword, $generatedPassword)
    {
        if ($newPassword) {
            return $newPassword;
        }
        if ($generatedPassword) {
            return $this->random->getRandomString(8);
        }
    }

    /**
     * @param $supplierId
     * @return string
     */
    public function getSupplierInformationHtml($supplierId)
    {
        $supplier = $this->supplierRepositoryInterface->getById($supplierId);
        $supplierInformation = $supplier->getSupplierName().' ('.$supplier->getSupplierCode().')<br />';
        if ($supplier->getStreet()) {
            $supplierInformation .= $supplier->getStreet();
        }
        if ($supplier->getCity()) {
            $supplierInformation .= ', '.$supplier->getCity();
        }
        if ($supplier->getPostcode()) {
            $supplierInformation .= ', '.$supplier->getPostcode();
        }
        if ($supplier->getRegionId()) {
            /** @var \Magento\Directory\Model\Region $regionModel */
            $regionModel = $this->regionFactory->create();
            $regionData = $regionModel->loadByCode($supplier->getRegionId(), $supplier->getCountryId());
            if ($regionData->getName()) {
                $supplierInformation .= ', '.$regionData->getName();
            }
        } elseif ($supplier->getRegion()) {
            $supplierInformation .= ', '.$supplier->getRegion();
        }
        if ($supplier->getCountryId()) {
            /** @var \Magento\Directory\Model\Country $countryModel */
            $countryModel = $this->countryFactory->create();
            $countryData = $countryModel->loadByCode($supplier->getCountryId());
            if ($countryData->getName()) {
                $supplierInformation .= '<br />'.$countryData->getName();
            }
        }

        return $supplierInformation;
    }

    /**
     * get list supplier to pick items
     * $productIds = [$itemId => $productId]
     * return [$itemId => [$supplierId]]
     *
     * @param array $productIds
     * @return array
     */
    public function getSuppliersToDropship($productIds)
    {
        /* transfer data to item-supplier */
        $itemSupplier = [];
        foreach($productIds as $itemId => $productId) {
            /** @var \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface $supplierRepository */
            $supplierRepository = $this->objectManager->create(
                '\Magestore\SupplierSuccess\Api\SupplierRepositoryInterface'
            );
            $supplierCollection = $supplierRepository->getSupplierByProductId($productId);
            if ($supplierCollection->getSize()) {
                foreach ($supplierCollection as $supplier) {
                    $itemSupplier[$itemId][$supplier->getId()]['supplier'] = $supplier->getData();
                }
            }
        }

        return $itemSupplier;
    }

    /**
     * send new password to supplier
     * @param $supplier
     * @param $newPassword
     */
    public function sendNewPasswordTosupplier($supplier, $newPassword)
    {
        $this->emailService->sendNewPasswordTosupplier($supplier, $newPassword);
    }
}