<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Model\Supplier;

class PricelistUpload extends \Magento\Framework\Model\AbstractModel
    implements \Magestore\DropshipSuccess\Api\Data\SupplierPricelistUploadInterface
{

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\DropshipSuccess\Model\ResourceModel\Supplier\PricelistUpload');
    }

    /**#@-*/

    /**
     * Supplier Pricelist Upload id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_getData(self::SUPPLIER_PRICELIST_UPLOAD_ID);
    }

    /**
     * Set Supplier Pricelist Upload id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::SUPPLIER_PRICELIST_UPLOAD_ID, $id);
    }


    /**
     * Supplier Id
     *
     * @return int|null
     */
    public function getSupplierId()
    {
        return $this->_getData(self::SUPPLIER_ID);
    }

    /**
     * Set supplier id
     *
     * @param string $supplierId
     * @return $this
     */
    public function setSupplierId($supplierId)
    {
        return $this->setData(self::SUPPLIER_ID, $supplierId);
    }

    /**
     * Title
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->_getData(self::TITLE);
    }

    /**
     * Set Title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * File upload
     *
     * @return string|null
     */
    public function getFileUpload()
    {
        return $this->_getData(self::FILE_UPLOAD);
    }

    /**
     * Set File Upload
     *
     * @param string $fileUpload
     * @return $this
     */
    public function setFileUpload($fileUpload)
    {
        return $this->setData(self::FILE_UPLOAD, $fileUpload);
    }

    /**
     * Created At
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * Set Created At
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}