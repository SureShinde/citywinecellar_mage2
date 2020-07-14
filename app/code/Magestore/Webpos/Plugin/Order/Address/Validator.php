<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Plugin\Order\Address;

/**
 * Class Validator
 */
/**
 * Class Validator
 * @package Magestore\Webpos\Plugin\Order\Address
 */
class Validator
{
    /**
     *
     */
    const WEBPOS_PATH = 'webpos';

    /**
     * @var \Magento\Framework\Webapi\Rest\Request
     */
    protected $request;

    /**
     * Validator constructor.
     * @param \Magento\Framework\Webapi\Rest\Request $request
     */
    public function __construct(
        \Magento\Framework\Webapi\Rest\Request $request
    ){
        $this->request = $request;
    }

    /**
     * @param \Magento\Sales\Model\Order\Address\Validator $subject
     * @param \Closure $proceed
     * @param \Magento\Sales\Model\Order\Address $address
     * @return array
     */
    public function aroundValidate(
        \Magento\Sales\Model\Order\Address\Validator $subject,
        \Closure $proceed,
        \Magento\Sales\Model\Order\Address $address
    ){
        $pathInfo = $this->request->getPathInfo();
        if (strpos($pathInfo, self::WEBPOS_PATH) !== false) {
            $isPosRequest = true;
        } else {
            $isPosRequest = false;
        }
        if ($isPosRequest && !$address->getCustomerId()) {
            return [];
        } else {
            return $proceed($address);
        }
    }
}
