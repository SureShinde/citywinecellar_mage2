<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\PaymentOffline\Helper;

/**
 * Class Data
 * @package Magestore\PaymentOffline\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var
     */
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
    )
    {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->enableOption = $enableOption;
        $this->useReferenceNumberOption = $useReferenceNumberOption;
        $this->usePayLaterOption = $usePayLaterOption;
        $this->storeManager = $storeManager;
    }

    /**
     * @param null $icon
     * @return string
     */
    public function getIconPath($icon = null)
    {
        $iconPath = $this->filesystem->getDirectoryRead('media')->getAbsolutePath('webpos/paymentoffline/icon/');
        if ($icon) {
            return $iconPath . $icon;
        } else {
            $iconPaymentOfflinePath = $this->filesystem->getDirectoryRead('media')->getAbsolutePath('webpos/paymentoffline/');
            if (!is_dir($iconPaymentOfflinePath)) {
                mkdir($iconPaymentOfflinePath, 0777);
            }
            if (!is_dir($iconPath)) {
                mkdir($iconPath, 0777);
            }
            return $iconPath;
        }
    }

    /**
     * @param null $icon
     * @return string
     */
    public function getIconUrl($icon = null)
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl.'webpos/paymentoffline/icon/'.$icon;
    }

    /**
     * @return array
     */
    public function getEnableOption()
    {
        return $this->enableOption->toOptionArray();
    }

    /**
     * @return array
     */
    public function getUseReferenceNumberOption()
    {
        return $this->useReferenceNumberOption->toOptionArray();
    }

    /**
     * @return array
     */
    public function getPayLaterOption()
    {
        return $this->usePayLaterOption->toOptionArray();
    }
}
