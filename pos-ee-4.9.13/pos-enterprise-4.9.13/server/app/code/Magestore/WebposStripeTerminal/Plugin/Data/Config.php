<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposStripeTerminal\Plugin\Data;

use Magestore\WebposStripeTerminal\Api\ConnectedReaderRepositoryInterface;
use Magestore\WebposStripeTerminal\Api\Data\ConnectedReaderInterface;

/**
 * Class Config
 *
 * @package Magestore\WebposStripeTerminal\Plugin\Data
 */
class Config
{
    /**
     * @var \Magestore\WebposStripeTerminal\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Config constructor.
     *
     * @param \Magestore\WebposStripeTerminal\Helper\Data $helper
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magestore\WebposStripeTerminal\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->helper = $helper;
        $this->request = $request;
    }

    /**
     * After get settings
     *
     * @param \Magestore\Webpos\Model\Config\Config $subject
     * @param mixed $result
     * @return \Magestore\Webpos\Api\Data\Config\SystemConfigInterface[]
     */
    public function afterSetSettings($subject, $result)
    {
        /** @var \Magento\Framework\App\ObjectManager $objectManager */
        $objectManager = $this->helper->getObjectManager();
        /** @var \Magestore\Webpos\Api\Data\Config\SystemConfigInterface[] $settings */
        $settings = $subject->getSettings();
        /** @var \Magestore\Webpos\Api\Data\Config\SystemConfigInterface $setting */
        $setting = $objectManager->create(\Magestore\Webpos\Api\Data\Config\SystemConfigInterface::class);
        $setting->setPath($this->helper->getConfigPath('enable'));
        $setting->setValue($this->helper->isEnabled());
        $settings[] = $setting;

        /** @var \Magestore\Webpos\Api\Data\Config\SystemConfigInterface $setting */
        $setting = $objectManager->create(\Magestore\Webpos\Api\Data\Config\SystemConfigInterface::class);
        $setting->setPath($this->helper->getConfigPath('stripe_location_id'));
        /** @var \Magestore\WebposStripeTerminal\Api\StripeTerminalServiceInterface $stripeTerminalService */
        $stripeTerminalService = $objectManager->create(
            \Magestore\WebposStripeTerminal\Api\StripeTerminalServiceInterface::class
        );
        $setting->setValue($stripeTerminalService->getStripeLocationId() ?: '');
        $settings[] = $setting;

        /** @var ConnectedReaderRepositoryInterface $connectedReaderRepository */
        $connectedReaderRepository = $objectManager->create(ConnectedReaderRepositoryInterface::class);
        /** @var ConnectedReaderInterface $connectedReader */
        $curSession = $this->helper->getCurrentSession();
        if ($curSession) {
            $posId = $curSession->getPosId();
        } else {
            $posId = $this->request->getParam(\Magestore\Webpos\Model\Checkout\PosOrder::PARAM_ORDER_POS_ID);
        }
        $connectedReader = $connectedReaderRepository->getPosByPosId($posId);

        if (!$connectedReader->getReaderId()) {
            $subject->setData('settings', $settings);
            return $result;
        }
        /** @var \Magestore\Webpos\Api\Data\Config\SystemConfigInterface $setting */
        $setting = $objectManager->create(\Magestore\Webpos\Api\Data\Config\SystemConfigInterface::class);
        $setting->setPath($this->helper->getConfigPath('connected_reader'));
        $setting->setValue(
            json_encode(
                [
                    'id' => $connectedReader->getReaderId(),
                    'label' => $connectedReader->getReaderLabel(),
                    ConnectedReaderInterface::IP_ADDRESS => $connectedReader->getIpAddress(),
                    ConnectedReaderInterface::SERIAL_NUMBER=> $connectedReader->getSerialNumber(),
                ]
            )
        );
        $settings[] = $setting;
        $subject->setData('settings', $settings);
        return $result;
    }
}
