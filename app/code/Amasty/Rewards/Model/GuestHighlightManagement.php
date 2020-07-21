<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model;

use Amasty\Rewards\Api\Data\HighlightInterfaceFactory;
use Amasty\Rewards\Api\GuestHighlightManagementInterface;
use Amasty\Rewards\Api\RuleRepositoryInterface;
use Amasty\Rewards\Helper\Data;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Store\Model\StoreManagerInterface;

class GuestHighlightManagement implements GuestHighlightManagementInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var HighlightInterfaceFactory
     */
    private $highlightFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Config $config,
        RuleRepositoryInterface $ruleRepository,
        HighlightInterfaceFactory $highlightFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->ruleRepository = $ruleRepository;
        $this->highlightFactory = $highlightFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function getHighlight($page)
    {
        if (!$this->isVisible($page)) {
            return null;
        }

        $rules = $this->ruleRepository->getRulesByAction(
            Data::REGISTRATION_ACTION,
            $this->storeManager->getStore()->getWebsiteId(),
            GroupInterface::NOT_LOGGED_IN_ID
        );

        $amount = 0;

        /** @var \Amasty\Rewards\Api\Data\RuleInterface $rule */
        foreach ($rules as $rule) {
            $amount += $rule->getAmount();
        }

        /** @var \Amasty\Rewards\Api\Data\HighlightInterface $highlight */
        $highlight = $this->highlightFactory->create();
        $highlight->setVisible((bool) $amount)
            ->setCaptionColor($this->config->getHighlightColor($this->getStoreId()))
            ->setCaptionText(__('%1 Reward Points', $amount));

        return $highlight;
    }

    /**
     * @inheritDoc
     */
    public function isVisible($page)
    {
        switch ($page) {
            case self::PAGE_PRODUCT:
                $isVisibleOnPage = $this->config->getHighlightProductVisibility($this->getStoreId());
                break;
            case self::PAGE_CART:
                $isVisibleOnPage = $this->config->getHighlightCartVisibility($this->getStoreId());
                break;
            case self::PAGE_CHECKOUT:
                $isVisibleOnPage = $this->config->getHighlightCheckoutVisibility($this->getStoreId());
                break;
            default:
                throw new \InvalidArgumentException(__('Invalid page argument value.'));
        }

        return $this->config->isHighlightGuestVisibility($this->getStoreId()) && $isVisibleOnPage;
    }

    /**
     * @return int
     */
    private function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }
}
