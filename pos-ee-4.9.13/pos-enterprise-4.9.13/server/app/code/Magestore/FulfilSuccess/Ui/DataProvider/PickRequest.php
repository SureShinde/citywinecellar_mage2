<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Ui\DataProvider;

/**
 * Class AbstractProvider
 * @package Magestore\FulfilSuccess\Ui\DataProvider\PickRequest
 */
class PickRequest extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @return array
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
//        echo "<pre>";
//        var_dump($meta);die;
        return $meta;
    }
}