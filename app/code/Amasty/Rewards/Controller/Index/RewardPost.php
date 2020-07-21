<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Controller\Index;

use Magento\Checkout\Controller\Cart;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Controller for cancelled Rewards
 */
class RewardPost extends Cart
{
    /**
     * @var \Amasty\Rewards\Model\Quote
     */
    protected $rewardsQuote;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Amasty\Rewards\Api\CheckoutRewardsManagementInterface
     */
    private $rewardsManagement;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Amasty\Rewards\Model\Quote $quote,
        \Psr\Log\LoggerInterface $logger,
        \Amasty\Rewards\Api\CheckoutRewardsManagementInterface $rewardsManagement
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->rewardsQuote = $quote;
        $this->logger = $logger;
        $this->rewardsManagement = $rewardsManagement;
    }

    /**
     * Initialize coupon
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $applyCode = $this->getRequest()->getParam('remove') == 1 ? 0 : 1;
        $cartQuote = $this->_checkoutSession->getQuote();
        $usedPoints = $this->getRequest()->getParam('amreward_amount', 0);

        try {
            if ($applyCode) {
                $pointsData = $this->rewardsManagement->set($cartQuote->getId(), $usedPoints);
                $this->messageManager->addNoticeMessage(__($pointsData['notice']));
            } else {
                $itemsCount = $cartQuote->getItemsCount();

                if ($itemsCount) {
                    $this->rewardsManagement->collectCurrentTotals($cartQuote, 0);
                }

                $this->rewardsQuote->addReward(
                    $cartQuote->getId(),
                    0
                );

                $this->messageManager->addSuccessMessage(__('You Canceled Reward'));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('We cannot Reward.'));
            $this->logger->critical($e);
        }

        return $this->_goBack();
    }
}
