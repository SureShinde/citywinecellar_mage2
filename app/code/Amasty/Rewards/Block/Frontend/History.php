<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Block\Frontend;

use Magento\Customer\Controller\RegistryConstants;

class History extends \Magento\Framework\View\Element\Template
{
    /**
     * @var array
     */
    protected $rewards = [];

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Amasty\Rewards\Model\ResourceModel\Rewards\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Amasty\Rewards\Model\Date
     */
    private $date;

    /**
     * @var null|int
     */
    private $nonExpireId = null;

    /**
     * @var \Amasty\Rewards\Model\ResourceModel\Expiration
     */
    private $expirationResource;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Rewards\Model\ResourceModel\Expiration $expirationResource,
        \Amasty\Rewards\Model\ResourceModel\Rewards\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        \Amasty\Rewards\Model\Date $date,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->expirationResource = $expirationResource;
        $this->collectionFactory = $collectionFactory;
        $this->coreRegistry = $registry;
        $this->date = $date;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Rewards History'));
    }

    /**
     * @return \Amasty\Rewards\Model\Rewards[]|null
     */
    public function getRewards()
    {
        if (!($customerId = $this->getCustomerId())) {
            return false;
        }

        if (!$this->rewards) {
            $collection = $this->collectionFactory->create();
            $this->rewards = $collection->addCustomerIdFilter($customerId)
                ->addExpiration($this->date->getDateWithOffsetByDays(0))
                ->getItems();
        }

        $this->nonExpireId = $this->expirationResource->getNonExpireId($this->getCustomerId());

        return $this->rewards;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }

    public function getCustomerId()
    {
        return $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * @param string $date
     *
     * @return string
     */
    public function formatDateByLocal($date)
    {
        $storeCode = $this->_storeManager->getStore()->getCode();

        return $this->date->convertDate($date, $storeCode);
    }

    /**
     * @param \Amasty\Rewards\Model\Rewards $reward
     *
     * @return string
     */
    public function getExpirationLabel($reward)
    {
        $label = null;

        if ($reward->getDaysLeft() !== null) {
            $label = __('expire in %1 day(s)', $reward->getDaysLeft());
        } elseif ($reward->getExpirationId() != $this->nonExpireId && $reward->getExpirationId() != 0) {
            $label = __('expired');
        }

        return $label;
    }

    /**
     * @param \Amasty\Rewards\Model\Rewards $reward
     *
     * @return string
     */
    public function getExpirationLabelClass($reward)
    {
        $class = null;

        if ($reward->getDaysLeft() === null) {
            $class = '-expired';
        } elseif ($reward->getDaysLeft() > 3) {
            $class = '-warning';
        } else {
            $class = '-critical';
        }

        return $class;
    }

    /**
     * @param \Amasty\Rewards\Model\Rewards $reward
     *
     * @return bool
     */
    public function canExpire($reward)
    {
        if ($reward->getExpirationId() == $this->nonExpireId || $reward->getExpirationId() == 0) {
            return false;
        }

        return true;
    }
}
