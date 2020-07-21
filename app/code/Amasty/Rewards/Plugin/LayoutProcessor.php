<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Plugin;

use Amasty\Rewards\Api\CheckoutHighlightManagementInterface;
use Amasty\Rewards\Api\GuestHighlightManagementInterface;
use Amasty\Rewards\Helper\Data;
use Amasty\Rewards\Model\Config as ConfigProvider;

class LayoutProcessor
{
    use \Amasty\Rewards\Model\ArrayPathTrait;

    /**
     * @var Data
     */
    private $data;

    /**
     * @var CheckoutHighlightManagementInterface
     */
    private $highlightManagement;

    /**
     * @var GuestHighlightManagementInterface
     */
    private $guestHighlightManagement;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Data $data,
        CheckoutHighlightManagementInterface $highlightManagement,
        GuestHighlightManagementInterface $guestHighlightManagement,
        ConfigProvider $configProvider
    ) {
        $this->data = $data;
        $this->highlightManagement = $highlightManagement;
        $this->guestHighlightManagement = $guestHighlightManagement;
        $this->configProvider = $configProvider;
    }

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param $result
     * @return mixed
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        $result
    ) {
        $highlightPath = 'components/checkout/./sidebar/./summary/./amasty-rewards-highlight';

        if (!$this->configProvider->isEnabled()) {
            $this->unsetArrayValueByPath(
                $result,
                'components/checkout/./steps/./billing-step/./payment/./afterMethods/./rewards'
            );
            $this->unsetArrayValueByPath(
                $result,
                $highlightPath
            );

            return $result;
        }

        $this->setToArrayByPath(
            $result,
            'components/checkout/./steps/./billing-step/./payment/./afterMethods/./rewards',
            $this->data->getRewardsData()
        );

        if ($this->highlightManagement->isVisible(CheckoutHighlightManagementInterface::CHECKOUT)) {
            $this->setToArrayByPath(
                $result,
                $highlightPath,
                $this->highlightManagement->getHighlightData()
            );
        } elseif ($this->guestHighlightManagement->isVisible(GuestHighlightManagementInterface::PAGE_CHECKOUT)) {
            $this->setToArrayByPath(
                $result,
                $highlightPath . '/component',
                'Amasty_Rewards/js/guest-highlight',
                false
            );
            $this->setToArrayByPath(
                $result,
                $highlightPath . '/highlight',
                $this->guestHighlightManagement
                    ->getHighlight(GuestHighlightManagementInterface::PAGE_CHECKOUT)
                    ->getData()
            );
        } else {
            $this->unsetArrayValueByPath($result, $highlightPath);
        }

        return $result;
    }
}
