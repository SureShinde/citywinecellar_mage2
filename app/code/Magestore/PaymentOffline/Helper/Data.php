<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PaymentOffline\Helper;

/**
 * Helper Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $context;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magestore\PaymentOffline\Model\Source\Adminhtml\Enable
     */
    protected $enableOption;

    /**
     * @var \Magestore\PaymentOffline\Model\Source\Adminhtml\UseReferenceNumber
     */
    protected $useReferenceNumberOption;

    /**
     * @var \Magestore\PaymentOffline\Model\Source\Adminhtml\UsePayLater
     */
    protected $usePayLaterOption;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magestore\PaymentOffline\Model\Source\Adminhtml\Enable $enableOption
     * @param \Magestore\PaymentOffline\Model\Source\Adminhtml\UseReferenceNumber $useReferenceNumberOption
     * @param \Magestore\PaymentOffline\Model\Source\Adminhtml\UsePayLater $usePayLaterOption
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magestore\PaymentOffline\Model\Source\Adminhtml\Enable $enableOption,
        \Magestore\PaymentOffline\Model\Source\Adminhtml\UseReferenceNumber $useReferenceNumberOption,
        \Magestore\PaymentOffline\Model\Source\Adminhtml\UsePayLater $usePayLaterOption,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->enableOption = $enableOption;
        $this->useReferenceNumberOption = $useReferenceNumberOption;
        $this->usePayLaterOption = $usePayLaterOption;
        $this->storeManager = $storeManager;
    }

    /**
     * Get Icon Path
     *
     * @param string $icon
     * @return string
     */
    public function getIconPath($icon = null)
    {
        $iconPath = $this->filesystem->getDirectoryRead('media')
            ->getAbsolutePath('webpos/paymentoffline/icon/');
        if ($icon) {
            return $iconPath . $icon;
        } else {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var \Magento\Framework\Filesystem\DriverInterface $driverInterface */
            $driverInterface = $objectManager->get(\Magento\Framework\Filesystem\DriverInterface::class);
            $iconPaymentOfflinePath = $this->filesystem->getDirectoryRead('media')
                ->getAbsolutePath('webpos/paymentoffline/');
            if (!$driverInterface->isDirectory($iconPaymentOfflinePath)) {
                $driverInterface->createDirectory($iconPaymentOfflinePath, 0777);
            }
            if (!$driverInterface->isDirectory($iconPath)) {
                $driverInterface->createDirectory($iconPath, 0777);
            }
            return $iconPath;
        }
    }

    /**
     * Get Icon Url
     *
     * @param string $icon
     * @return string
     */
    public function getIconUrl($icon = null)
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl . 'webpos/paymentoffline/icon/' . $icon;
    }

    /**
     * Get Enable Option
     *
     * @return array
     */
    public function getEnableOption()
    {
        return $this->enableOption->toOptionArray();
    }

    /**
     * Get Use Reference Number Option
     *
     * @return array
     */
    public function getUseReferenceNumberOption()
    {
        return $this->useReferenceNumberOption->toOptionArray();
    }

    /**
     * Get Pay Later Option
     *
     * @return array
     */
    public function getPayLaterOption()
    {
        return $this->usePayLaterOption->toOptionArray();
    }
}
