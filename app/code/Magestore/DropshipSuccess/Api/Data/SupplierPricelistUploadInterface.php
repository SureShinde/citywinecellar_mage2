<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Api\Data;


interface SupplierPricelistUploadInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const SUPPLIER_PRICELIST_UPLOAD_ID = 'supplier_pricelist_upload_id';
    const SUPPLIER_ID = 'supplier_id';
    const TITLE = 'title';
    const FILE_UPLOAD = 'file_upload';
    const CREATED_AT = 'created_at';

    /**#@-*/

    /**
     * Supplier Pricelist Upload id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set Supplier Pricelist Upload id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);


    /**
     * Supplier Id
     *
     * @return int|null
     */
    public function getSupplierId();

    /**
     * Set supplier id
     *
     * @param string $supplierId
     * @return $this
     */
    public function setSupplierId($supplierId);

    /**
     * Title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Set Title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * File upload
     *
     * @return string|null
     */
    public function getFileUpload();

    /**
     * Set File Upload
     *
     * @param string $fileUpload
     * @return $this
     */
    public function setFileUpload($fileUpload);

    /**
     * Created At
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set Created At
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

}