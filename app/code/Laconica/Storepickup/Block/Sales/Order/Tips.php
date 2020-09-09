<?php

namespace Laconica\Storepickup\Block\Sales\Order;

use Laconica\Storepickup\Helper\Data;
use Magento\Checkout\Model\Session;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order;
use Magento\Store\Model\Store;
use Magento\Tax\Model\Config;

class Tips extends Template
{
    /**
     * @var Order
     */
    protected $order;

    /**
     * @var DataObject
     */
    protected $source;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Tips constructor.
     * @param Template\Context $context
     * @param Config $taxConfig
     * @param Session $session
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Config $taxConfig,
        Session $session,
        array $data = []
    ) {
        $this->config = $taxConfig;
        $this->session = $session;
        parent::__construct($context, $data);
    }

    /**
     * Check if we need display full tax total info
     *
     * @return bool
     */
    public function displayFullSummary()
    {
        return true;
    }

    /**
     * Get data (totals) source model
     *
     * @return DataObject
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return Store
     */
    public function getStore()
    {
        return $this->order->getStore();
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return array
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return array
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * Initialize all order totals relates with tax
     *
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->order = $parent->getOrder();
        $this->source = $parent->getSource();

        $tips = new DataObject(
            [
                'code' => Data::TIPS_CODE,
                'strong' => false,
                'value' => 0,
                'label' => __('Tips'),
            ]
        );

        $parent->addTotal($tips, Data::TIPS_CODE);

        return $this;
    }
}
