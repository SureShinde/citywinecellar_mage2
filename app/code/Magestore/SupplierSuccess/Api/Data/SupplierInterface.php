<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Api\Data;


interface SupplierInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const SUPPLIER_ID = 'supplier_id';

    const SUPPLIER_CODE = 'supplier_code';

    const SUPPLIER_NAME = 'supplier_name';

    const CONTACT_NAME = 'contact_name';

    const CONTACT_EMAIL = 'contact_email';

    const TELEPHONE = 'telephone';

    const FAX = 'fax';

    const STREET = 'street';

    const CITY = 'city';

    const COUNTRY_ID = 'country_id';

    const REGION_ID = 'region_id';

    const REGION = 'region';

    const POSTCODE = 'postcode';

    const WEBSITE = 'website';

    const DESCRIPTION = 'description';

    const STATUS = 'status';

    const PASSWORD = 'password';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    const CURRENT_SUPPLIER = 'current_supplier';

    /**#@-*/

    /**
     * Supplier id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set supplier id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Supplier code
     *
     * @return string
     */
    public function getSupplierCode();

    /**
     * Set supplier code
     *
     * @param string $supplierCode
     * @return $this
     */
    public function setSupplierCode($supplierCode);

    /**
     * Supplier name
     *
     * @return string|null
     */
    public function getSupplierName();

    /**
     * Set supplier name
     *
     * @param string $supplierName
     * @return $this
     */
    public function setSupplierName($supplierName);

    /**
     * Contact name
     *
     * @return string|null
     */
    public function getContactName();

    /**
     * Set contact name
     *
     * @param string $contactName
     * @return $this
     */
    public function setContactName($contactName);

    /**
     * Contact email
     *
     * @return string|null
     */
    public function getContactEmail();

    /**
     * Set contact email
     *
     * @param string $contactEmail
     * @return $this
     */
    public function setContactEmail($contactEmail);

    /**
     * Telephone
     *
     * @return string|null
     */
    public function getTelephone();

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return $this
     */
    public function setTelephone($telephone);

    /**
     * Fax
     *
     * @return string|null
     */
    public function getFax();

    /**
     * Set fax
     *
     * @param string $fax
     * @return $this
     */
    public function setFax($fax);

    /**
     * Street
     *
     * @return string|null
     */
    public function getStreet();

    /**
     * Set street
     *
     * @param string $street
     * @return $this
     */
    public function setStreet($street);

    /**
     * City
     *
     * @return string|null
     */
    public function getCity();

    /**
     * Set City
     *
     * @param string $city
     * @return $this
     */
    public function setCity($city);

    /**
     * Country Id
     *
     * @return string|null
     */
    public function getCountryId();

    /**
     * Set Country Id
     *
     * @param string $countryId
     * @return $this
     */
    public function setCountryId($countryId);

    /**
     * Region Id
     *
     * @return string|null
     */
    public function getRegionId();

    /**
     * Set Region Id
     *
     * @param string $regionId
     * @return $this
     */
    public function setRegionId($regionId);

    /**
     * Region
     *
     * @return string|null
     */
    public function getRegion();

    /**
     * Set Region
     *
     * @param string $region
     * @return $this
     */
    public function setRegion($region);

    /**
     * Postcode
     *
     * @return string|null
     */
    public function getPostcode();

    /**
     * Set Postcode
     *
     * @param string $postcode
     * @return $this
     */
    public function setPostcode($postcode);

    /**
     * Website
     *
     * @return string|null
     */
    public function getWebsite();

    /**
     * Set Website
     *
     * @param string $website
     * @return $this
     */
    public function setWebsite($website);

    /**
     * Description
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Set Description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Set Status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Password
     *
     * @return string|null
     */
    public function getPassword();

    /**
     * Set Password
     *
     * @param string $password
     * @return $this
     */
    public function setPassword($password);

    /**
     * Created at
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set Created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Updated at
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set Updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);
}