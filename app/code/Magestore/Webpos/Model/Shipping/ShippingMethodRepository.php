<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Shipping;
/**
 * Class ShippingMethodRepository
 * @package Magestore\Webpos\Model\Shipping
 */
class ShippingMethodRepository implements \Magestore\Webpos\Api\Shipping\ShippingMethodRepositoryInterface
{
    protected $shippingMethodSearchResults;
    protected $shippingMethodSource;

    public function __construct(
        \Magestore\Webpos\Model\Source\Adminhtml\Shipping $shippingMethodSource,
        \Magento\Framework\Api\SearchResults $shippingMethodSearchResults

    ){
        $this->shippingMethodSearchResults = $shippingMethodSearchResults;
        $this->shippingMethodSource = $shippingMethodSource;
    }

    /**
     * Retrieve shipping matching the specified criteria.
     *
     * @return \Magento\Framework\Api\SearchResults
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList()
    {
        $shippingList = $this->shippingMethodSource->getPosShippingMethods();
        $shippings = $this->shippingMethodSearchResults;
        $shippings->setItems($shippingList);
        $shippings->setTotalCount(count($shippingList));
        return $shippings;
    }

}