<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Config;

interface CustomerGroupInterface {
    const ID = 'id';
    const CODE = 'code';
    const TAX_CLASS_ID = 'tax_class_id';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return CustomerGroupInterface
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     * @return CustomerGroupInterface
     */
    public function setCode($code);

    /**
     * @return int
     */
    public function getTaxClassId();

    /**
     * @param int $taxClassId
     * @return CustomerGroupInterface
     */
    public function setTaxClassId($taxClassId);
}