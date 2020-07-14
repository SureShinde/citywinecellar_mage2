<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Payment\Model\Payment\Type;

/**
 * Class AbstractType
 * @package Magestore\Payment\Model\Payment\Type
 */
abstract class AbstractType
{
    /**
     * @var array
     */
    protected $data;

    /**
     * AbstractType constructor.
     * @param array $data
     */
    public function __construct(
        array $data = []
    )
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
