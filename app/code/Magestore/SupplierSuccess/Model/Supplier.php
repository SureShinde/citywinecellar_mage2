<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Model;

class Supplier extends \Magento\Framework\Model\AbstractModel
    implements \Magestore\SupplierSuccess\Api\Data\SupplierInterface
{

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\SupplierSuccess\Model\ResourceModel\Supplier');
    }

    /**
     * Identifier getter
     *
     * @return int
     */
    public function getId()
    {
        return $this->_getData('supplier_id');
    }

    /**
     * Set entity Id
     *
     * @param int $value
     * @return $this
     */
    public function setId($value)
    {
        return $this->setData('supplier_id', $value);
    }

    /**
     * Get supplier code
     *
     * @return string
     */
    public function getSupplierCode()
    {
        return $this->_getData(self::SUPPLIER_CODE);
    }

    /**
     * Set supplier code
     *
     * @param string $supplierCode
     * @return $this
     */
    public function setSupplierCode($supplierCode)
    {
        return $this->setData(self::SUPPLIER_CODE, $supplierCode);
    }

    /**
     * Supplier name
     *
     * @return string|null
     */
    public function getSupplierName()
    {
        return $this->_getData(self::SUPPLIER_NAME);
    }

    /**
     * Set supplier name
     *
     * @param string $supplierName
     * @return $this
     */
    public function setSupplierName($supplierName)
    {
        return $this->setData(self::SUPPLIER_NAME, $supplierName);
    }

    /**
     * Contact name
     *
     * @return string|null
     */
    public function getContactName()
    {
        return $this->_getData(self::CONTACT_NAME);
    }

    /**
     * Set contact name
     *
     * @param string $contactName
     * @return $this
     */
    public function setContactName($contactName)
    {
        return $this->setData(self::CONTACT_NAME, $contactName);
    }

    /**
     * Contact email
     *
     * @return string|null
     */
    public function getContactEmail()
    {
        return $this->_getData(self::CONTACT_EMAIL);
    }

    /**
     * Set contact email
     *
     * @param string $contactEmail
     * @return $this
     */
    public function setContactEmail($contactEmail)
    {
        return $this->setData(self::CONTACT_EMAIL, $contactEmail);
    }

    /**
     * Telephone
     *
     * @return string|null
     */
    public function getTelephone()
    {
        return $this->_getData(self::TELEPHONE);
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return $this
     */
    public function setTelephone($telephone)
    {
        return $this->setData(self::TELEPHONE, $telephone);
    }

    /**
     * Fax
     *
     * @return string|null
     */
    public function getFax()
    {
        return $this->_getData(self::FAX);
    }

    /**
     * Set fax
     *
     * @param string $fax
     * @return $this
     */
    public function setFax($fax)
    {
        return $this->setData(self::FAX, $fax);
    }

    /**
     * Street
     *
     * @return string|null
     */
    public function getStreet()
    {
        return $this->_getData(self::STREET);
    }

    /**
     * Set street
     *
     * @param string $street
     * @return $this
     */
    public function setStreet($street)
    {
        return $this->setData(self::STREET, $street);
    }

    /**
     * City
     *
     * @return string|null
     */
    public function getCity()
    {
        return $this->_getData(self::CITY);
    }

    /**
     * Set City
     *
     * @param string $city
     * @return $this
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * Country Id
     *
     * @return string|null
     */
    public function getCountryId()
    {
        return $this->_getData(self::COUNTRY_ID);
    }

    /**
     * Set Country Id
     *
     * @param string $countryId
     * @return $this
     */
    public function setCountryId($countryId)
    {
        return $this->setData(self::COUNTRY_ID, $countryId);
    }

    /**
     * Region Id
     *
     * @return string|null
     */
    public function getRegionId()
    {
        return $this->_getData(self::REGION_ID);
    }

    /**
     * Set Region Id
     *
     * @param string $regionId
     * @return $this
     */
    public function setRegionId($regionId)
    {
        return $this->setData(self::REGION_ID, $regionId);
    }

    /**
     * Region
     *
     * @return string|null
     */
    public function getRegion()
    {
        return $this->_getData(self::REGION);
    }

    /**
     * Set Region
     *
     * @param string $region
     * @return $this
     */
    public function setRegion($region)
    {
        return $this->setData(self::REGION, $region);
    }

    /**
     * Postcode
     *
     * @return string|null
     */
    public function getPostcode()
    {
        return $this->_getData(self::POSTCODE);
    }

    /**
     * Set Postcode
     *
     * @param string $postcode
     * @return $this
     */
    public function setPostcode($postcode)
    {
        return $this->setData(self::POSTCODE, $postcode);
    }

    /**
     * Website
     *
     * @return string|null
     */
    public function getWebsite()
    {
        return $this->_getData(self::WEBSITE);
    }

    /**
     * Set Website
     *
     * @param string $website
     * @return $this
     */
    public function setWebsite($website)
    {
        return $this->setData(self::WEBSITE, $website);
    }

    /**
     * Description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->_getData(self::DESCRIPTION);
    }

    /**
     * Set Description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Status
     *
     * @return int|null
     */
    public function getStatus()
    {
        return $this->_getData(self::STATUS);
    }

    /**
     * Set Status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Password
     *
     * @return string|null
     */
    public function getPassword()
    {
        return $this->_getData(self::PASSWORD);
    }

    /**
     * Set Password
     *
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        return $this->setData(self::PASSWORD, $password);
    }

    /**
     * Created at
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * Set Created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Updated at
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->_getData(self::UPDATED_AT);
    }

    /**
     * Set Updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}