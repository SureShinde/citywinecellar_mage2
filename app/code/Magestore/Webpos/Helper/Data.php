<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;

/**
 * Class Data
 *
 * Used for helper data
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const URL_TYPE_PWA = 'apps/pos';
    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->objectManager = $objectManager;
        $this->_storeManager = $storeManager;
    }

    /**
     * Get model
     *
     * @param string $modelName
     * @param array $arguments
     * @return mixed
     * @throws \Magento\Framework\Exception\ValidatorException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getModel($modelName, array $arguments = [])
    {
        $model = $this->objectManager->create('\Magestore\Webpos\\' . $modelName);
        if (!$model) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('%1 doesn\'t extends \Magento\Framework\Model\AbstractModel', $modelName)
            );
        }
        return $model;
    }

    /**
     * Get config
     *
     * @param string $path
     * @return string
     */
    public function getStoreConfig($path)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getCurrentStoreView()->getId()
        );
    }

    /**
     * Get current location id
     *
     * @return mixed
     */
    public function getCurrentLocationId()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $sessionRepository = $objectManager->get(\Magestore\Webpos\Api\Staff\SessionRepositoryInterface::class);
        $request = $objectManager->get(\Magento\Framework\App\RequestInterface::class);
        try {
            $session = $sessionRepository->getBySessionId(
                $request->getParam(\Magestore\WebposIntegration\Controller\Rest\RequestProcessor::SESSION_PARAM_KEY)
            );
            $locationId = $session->getLocationId();
        } catch (\Exception $e) {
            $locationId = $request->getParam(\Magestore\Webpos\Model\Checkout\PosOrder::PARAM_ORDER_LOCATION_ID);
        }
        return $locationId;
    }

    /**
     * Get webpos images
     *
     * @return string
     */
    public function getWebPosImages()
    {
        return $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ) . 'webpos/logo/' . $this->getWebPosLogoSetting();
    }

    /**
     * Get pos logo
     *
     * @return mixed
     */
    public function getWebPosLogoSetting()
    {
        return $this->scopeConfig->getValue(
            'webpos/general/webpos_logo',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if store credit enable
     *
     * @return bool
     */
    public function isStoreCreditEnable()
    {
        if ($this->_moduleManager->isEnabled('Magestore_Customercredit')) {
            if ($this->getStoreConfig('customercredit/general/enable')) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if inventory success enable
     *
     * @return bool
     */
    public function isEnabledInventory()
    {
        return $this->_moduleManager->isEnabled('Magestore_InventorySuccess');
    }

    /**
     * Check if gift card enable
     *
     * @return bool
     */
    public function isEnabledGiftCard()
    {
        if ($this->_moduleManager->isEnabled('Magestore_Giftvoucher')) {
            if ($this->getStoreConfig('giftvoucher/general/active')) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if session enable
     *
     * @return bool
     */
    public function isEnableSession()
    {
        if ($this->getStoreConfig('webpos/session/enable')) {
            return true;
        }
        return false;
    }

    /**
     * Check if cash control enable
     *
     * @return bool
     */
    public function isCashControl()
    {
        if ($this->isEnableSession() &&
            $this->getStoreConfig('webpos/session/enable_cash_control')
        ) {
            return true;
        }
        return false;
    }

    /**
     * Get current store view
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getCurrentStoreView()
    {
        /** @var \Magento\Framework\Registry $registry */
        $registry = $this->objectManager->get(\Magento\Framework\Registry::class);
        if ($registry->registry('pos_api_url')
            && strstr($registry->registry('pos_api_url'), 'rest//')) {
            // api route does not contain store view code
            try {
                return $this->_storeManager->getDefaultStoreView();
            } catch (\Exception $e) {
                return $this->_storeManager->getStore();
            }
        }
        return $this->_storeManager->getStore();
    }

    /**
     * Get object manager
     *
     * @return \Magento\Framework\ObjectManagerInterface
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * Get pos url
     *
     * @return bool|string
     */
    public function getPosUrl()
    {
        $filesystem = $this->objectManager->create(\Magento\Framework\Filesystem::class);
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB)
            . $filesystem->getUri(DirectoryList::MEDIA);
        if (substr($mediaUrl, -strlen(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA . '/'))
            == \Magento\Framework\UrlInterface::URL_TYPE_MEDIA . '/') {
            $parentUrl = substr(
                $mediaUrl,
                0,
                -strlen(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA . '/')
            );
        }
        if (substr($mediaUrl, -strlen(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA))
            == \Magento\Framework\UrlInterface::URL_TYPE_MEDIA) {
            $parentUrl = substr($mediaUrl, 0, -strlen(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA));
        }
        $url = $parentUrl . self::URL_TYPE_PWA;
        return $url;
    }

    /**
     * Get pó relative url
     *
     * @return bool|mixed|string
     */
    public function getPosRelativeUrl()
    {
        $posUrl = $this->getPosUrl();
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $relativeUrl = str_replace($baseUrl, '', $posUrl);
        return $relativeUrl;
    }

    /**
     * Is Enabled Elastic Search Engine
     *
     * @return boolean
     */
    public function isEnableElasticSearch()
    {
        if (strpos($this->scopeConfig->getValue('catalog/search/engine'), 'elasticsearch') !== false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get product type ids to support
     *
     * @return array
     */
    public function getProductTypeIds()
    {
        $types = [
            \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
            \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
            \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE,
            \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,
            \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE
        ];
        if ($this->isEnabledGiftCard()) {
            $types[] = \Magestore\Giftvoucher\Model\Product\Type\Giftvoucher::GIFT_CARD_TYPE;
        }
        return $types;
    }

    /**
     * Check if barcode management enable
     *
     * @return bool
     */
    public function isEnabledBarcodeManagement()
    {
        return $this->_moduleManager->isEnabled('Magestore_BarcodeSuccess');
    }
}
