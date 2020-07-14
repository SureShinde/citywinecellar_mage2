<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magestore\Webpos\Block\Adminhtml\PosOrder;

use Magento\Directory\Model\Currency;
use Magento\Directory\Model\CurrencyFactory;
use Magestore\Webpos\Model\Source\Adminhtml\Location;
use Magestore\Webpos\Model\Source\Adminhtml\Pos;
use Magento\Sales\Model\Order\StatusFactory;

/**
 * Class \Magestore\Webpos\Block\Adminhtml\PosOrder\Detail
 */
class Detail extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magestore\Webpos\Model\Checkout\PosOrderFactory
     */
    protected $posOrderFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @var CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var Location
     */
    protected $location;

    /**
     * @var Pos
     */
    protected $pos;

    /**
     * @var StatusFactory
     */
    protected $statusFactory;

    /**
     * Detail constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magestore\Webpos\Model\Checkout\PosOrderFactory $posOrderFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param CurrencyFactory $currencyFactory
     * @param Location $location
     * @param Pos $pos
     * @param StatusFactory $statusFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magestore\Webpos\Model\Checkout\PosOrderFactory $posOrderFactory,
        \Magento\Framework\Serialize\Serializer\Json $json,
        CurrencyFactory $currencyFactory,
        Location $location,
        Pos $pos,
        StatusFactory $statusFactory,
        array $data = []
    ) {
        $this->posOrderFactory = $posOrderFactory;
        $this->json = $json;
        $this->currencyFactory = $currencyFactory;
        $this->location = $location;
        $this->pos = $pos;
        $this->statusFactory = $statusFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get Pos order data
     *
     * @return void
     */
    public function _construct()
    {
        $this->getPosOrderData();
        parent::_construct();
    }

    /**
     * Format price
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        $orderCurrencyCode = $this->getData('order_currency_code');
        $currencyModel = $this->getCurrency($orderCurrencyCode);
        if ($currencyModel->getId()) {
            return $currencyModel->format($price);
        } else {
            return $price;
        }
    }

    /**
     * Get location name
     *
     * @param int $locationId
     * @return string
     */
    public function getLocationName($locationId)
    {
        $locationOption = $this->location->getOptionArray();
        if (isset($locationOption[$locationId])) {
            return $locationOption[$locationId];
        } else {
            return '';
        }
    }

    /**
     * Get pos name
     *
     * @param int $posId
     * @return string
     */
    public function getPosName($posId)
    {
        $posOption = $this->pos->getOptionArray();
        if (isset($posOption[$posId])) {
            return $posOption[$posId];
        } else {
            return '';
        }
    }

    /**
     * Get label by code
     *
     * @param string $statusCode
     * @return string
     */
    public function getStatus($statusCode)
    {
        $status = $this->statusFactory->create()->load($statusCode, 'status');
        return $status->getLabel();
    }

    /**
     * Retrieve order currency
     *
     * @param string $code
     * @return Currency
     */
    public function getCurrency($code)
    {
        return $this->currencyFactory->create()->load($code);
    }

    /**
     * Get Pos order data
     *
     * @return void
     */
    public function getPosOrderData()
    {
        $posOrder = $this->getOrder();
        if ($posOrder->getId()) {
            $params = $posOrder->getParams();
            $decodeParams = $this->json->unserialize($params);
            if (isset($decodeParams['order'])) {
                $this->setData($decodeParams['order']);
            }
        }
    }

    /**
     * Get address description by type
     *
     * @param string $type
     * @return string
     */
    public function getAddressDescription($type)
    {
        $addresses = $this->getData('addresses');
        foreach ($addresses as $address) {
            if ($address['address_type'] == $type) {
                $addressArray = [];
                if (isset($address['street'][0])) {
                    $addressArray[] = $address['street'][0];
                }
                $addressArray[] = $address['city'];
                $addressArray[] = $address['region'];
                $addressArray[] = $address['postcode'];
                $addressArray[] = $address['country_id'];
                return implode(', ', $addressArray);
            }
        }
    }

    /**
     * Get Order
     *
     * @return mixed
     */
    public function getOrder()
    {
        return $this->posOrderFactory->create()->load($this->getRequest()->getParam('id'));
    }
}
