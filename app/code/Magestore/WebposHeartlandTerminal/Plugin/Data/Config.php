<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposHeartlandTerminal\Plugin\Data;

use Magestore\WebposHeartlandTerminal\Api\ConnectedReaderRepositoryInterface;
use Magestore\WebposHeartlandTerminal\Api\Data\ConnectedReaderInterface;

/**
 * Class Config
 *
 * @package Magestore\WebposHeartlandTerminal\Plugin\Data
 */
class Config
{
    /**
     * @var \Magestore\WebposHeartlandTerminal\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Config constructor.
     *
     * @param \Magestore\WebposHeartlandTerminal\Helper\Data $helper
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magestore\WebposHeartlandTerminal\Helper\Data $helper,
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

        /** @var ConnectedReaderRepositoryInterface $connectedReaderRepository */
        $connectedReaderRepository = $objectManager->create(ConnectedReaderRepositoryInterface::class);
        /** @var ConnectedReaderInterface $connectedReader */
        $curSession = $this->helper->getCurrentSession();
        if ($curSession) {
            $posId = $curSession->getPosId();
        } else {
            $posId = $this->request->getParam(\Magestore\Webpos\Model\Checkout\PosOrder::PARAM_ORDER_POS_ID);
        }
        $connectedReader = $connectedReaderRepository->getConnectedReaderByPosId($posId);
        if (!$connectedReader->getPort() && !$connectedReader->getSerialPort()) {
            $subject->setData('settings', $settings);
            return $result;
        }
        /** @var \Magestore\Webpos\Api\Data\Config\SystemConfigInterface $setting */
        $setting = $objectManager->create(\Magestore\Webpos\Api\Data\Config\SystemConfigInterface::class);
        $setting->setPath($this->helper->getConfigPath('connected_reader'));
        $setting->setValue(
            json_encode(
                [
                    ConnectedReaderInterface::POS_ID => $connectedReader->getPosId(),
                    ConnectedReaderInterface::IP_ADDRESS => $connectedReader->getIpAddress(),
                    ConnectedReaderInterface::PORT=> $connectedReader->getPort(),
                    ConnectedReaderInterface::SERIAL_PORT=> $connectedReader->getSerialPort(),
                    ConnectedReaderInterface::CONNECTION_MODE=> $connectedReader->getConnectionMode(),
                    ConnectedReaderInterface::SERIAL_PORT=> $connectedReader->getSerialPort(),
                ]
            )
        );
        $settings[] = $setting;
        $subject->setData('settings', $settings);
        return $result;
    }
}
