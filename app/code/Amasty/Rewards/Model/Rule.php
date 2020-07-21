<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model;

use Amasty\Rewards\Api\Data\RuleInterface;

/**
 * @method ResourceModel\Rule getResource()
 * @method ResourceModel\Rule _getResource()
 */
class Rule extends \Magento\Rule\Model\AbstractModel implements RuleInterface
{
    const BEFORE_TAX = 'before_tax';

    const AFTER_TAX = 'after_tax';

    const DEFAULT_BEHAVIOR = 0;

    const NEVER_EXPIRE_BEHAVIOR = 1;

    const CUSTOM_BEHAVIOR = 2;

    const STATUS_INACTIVE = 0;

    const STATUS_ACTIVE = 1;

    /**
     * @var \Magento\CatalogRule\Model\Rule\Condition\CombineFactory
     */
    private $combineFactory;

    /**
     * @var \Magento\CatalogRule\Model\Rule\Action\CollectionFactory
     */
    private $actionFactory;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    protected $serializer;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\SalesRule\Model\Rule\Condition\CombineFactory $combineFactory,
        \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $actionFactory,
        \Amasty\Base\Model\Serializer $serializer,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
        $this->combineFactory = $combineFactory;
        $this->actionFactory = $actionFactory;
        $this->serializer = $serializer;
    }

    protected function _construct()
    {
        $this->_init(\Amasty\Rewards\Model\ResourceModel\Rule::class);
        parent::_construct();
    }

    /**
     * {@inheritdoc}
     */
    public function getConditionsInstance()
    {
        return $this->combineFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getActionsInstance()
    {
        return $this->actionFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function getStoreLabel($storeId)
    {
        $labels = (array)$this->getStoreLabels();

        if (isset($labels[$storeId])) {
            return $labels[$storeId];
        } elseif (isset($labels[0]) && $labels[0]) {
            return $labels[0];
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function validateByCustomer($customer)
    {
        return in_array($customer->getWebsiteId(), $this->getWebsiteIds())
            && in_array(
                $customer->getGroupId(),
                $this->getCustomerGroupIds()
            );
    }

    /**
     * Set if not yet and retrieve rule store labels
     *
     * @return array
     */
    public function getStoreLabels()
    {
        if (!$this->hasStoreLabels()) {
            $labels = $this->_getResource()->getStoreLabels($this->getId());
            $this->setStoreLabels($labels);
        }

        return $this->_getData('store_labels');
    }

    public function activate()
    {
        $this->setIsActive(1);
        $this->save();

        return $this;
    }

    public function inactivate()
    {
        $this->setIsActive(0);
        $this->save();

        return $this;
    }

    public function loadByAction($action)
    {
        $this->addData($this->getResource()->loadByAction($action));

        return $this;
    }

    public function addDiscountDescription($address, $pointsUsed)
    {
        $description = $address->getDiscountDescriptionArray();
        $description['amrewards'] = __('Used %1 reward points', $pointsUsed);

        $address->setDiscountDescriptionArray($description);

        return $this;
    }

    /**
     * Get rule associated customer groups Ids
     *
     * @return array
     */
    public function getCustomerGroupIds()
    {
        if (!$this->hasCustomerGroupIds()) {
            $customerGroupIds = $this->_getResource()->getCustomerGroupIds($this->getId());
            $this->setData('customer_group_ids', (array)$customerGroupIds);
        }
        return $this->_getData('customer_group_ids');
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleId()
    {
        return $this->_getData(RuleInterface::RULE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRuleId($ruleId)
    {
        $this->setData(RuleInterface::RULE_ID, $ruleId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsActive()
    {
        return $this->_getData(RuleInterface::IS_ACTIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive($isActive)
    {
        $this->setData(RuleInterface::IS_ACTIVE, $isActive);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_getData(RuleInterface::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->setData(RuleInterface::NAME, $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConditionsSerialized()
    {
        return $this->_getData(RuleInterface::CONDITIONS_SERIALIZED);
    }

    /**
     * {@inheritdoc}
     */
    public function setConditionsSerialized($conditionsSerialized)
    {
        $this->setData(RuleInterface::CONDITIONS_SERIALIZED, $conditionsSerialized);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAction()
    {
        return $this->_getData(RuleInterface::ACTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setAction($action)
    {
        $this->setData(RuleInterface::ACTION, $action);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->_getData(RuleInterface::AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount($amount)
    {
        $this->setData(RuleInterface::AMOUNT, $amount);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpentAmount()
    {
        return $this->_getData(RuleInterface::SPENT_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setSpentAmount($spentAmount)
    {
        $this->setData(RuleInterface::SPENT_AMOUNT, $spentAmount);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPromoSkus()
    {
        return $this->_getData(RuleInterface::PROMO_SKUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPromoSkus($promoSkus)
    {
        $this->setData(RuleInterface::PROMO_SKUS, $promoSkus);

        return $this;
    }

    /**
     * @return array|null
     */
    public function getPromoSkusArray()
    {
        $promoSkus = $this->getPromoSkus();

        if ($promoSkus) {
            return \explode(',', $promoSkus); // в бд данные уже за`trim`лены
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getInactiveDays()
    {
        return $this->_getData(RuleInterface::INACTIVE_DAYS);
    }

    /**
     * {@inheritdoc}
     */
    public function setInactiveDays($days)
    {
        return $this->setData(RuleInterface::INACTIVE_DAYS, $days);
    }

    /**
     * {@inheritdoc}
     */
    public function getRecurring()
    {
        return $this->_getData(RuleInterface::RECURRING);
    }

    /**
     * {@inheritdoc}
     */
    public function setRecurring($status)
    {
        return $this->setData(RuleInterface::RECURRING, $status);
    }

    /**
     * @param int $behavior
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface
     */
    public function setExpirationBehavior($behavior)
    {
        return $this->setData(RuleInterface::EXPIRATION_BEHAVIOR, $behavior);
    }

    /**
     * @return int
     */
    public function getExpirationBehavior()
    {
        return $this->_getData(RuleInterface::EXPIRATION_BEHAVIOR);
    }

    /**
     * @param int $period
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface
     */
    public function setExpirationPeriod($period)
    {
        return $this->setData(RuleInterface::EXPIRATION_PERIOD, $period);
    }

    /**
     * @return int
     */
    public function getExpirationPeriod()
    {
        return $this->_getData(RuleInterface::EXPIRATION_PERIOD);
    }
}
