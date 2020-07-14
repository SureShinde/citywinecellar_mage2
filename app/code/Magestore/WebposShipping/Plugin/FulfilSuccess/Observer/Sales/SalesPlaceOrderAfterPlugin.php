<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposShipping\Plugin\FulfilSuccess\Observer\Sales;


class SalesPlaceOrderAfterPlugin
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * SalesPlaceOrderAfterPlugin constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->request = $request;
    }


    /**
     *  skip check picking
     *
     * @param \Magestore\FulfilSuccess\Observer\Sales\SalesPlaceOrderAfter $subject
     * @param boolean $result
     * @return boolean
     */
    public function afterCheckPicking(
        \Magestore\FulfilSuccess\Observer\Sales\SalesPlaceOrderAfter $subject,
        $result
    ) {

        if ($this->request->getParam('skip_check_picking')) {
            return false;
        }

        return $result;
    }
}