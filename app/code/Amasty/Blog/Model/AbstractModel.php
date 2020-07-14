<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model;

use Amasty\Blog\Helper\Settings;

class AbstractModel extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManagerInterface;

    /**
     * @var \Amasty\Blog\Helper\Url
     */
    private $urlHelper;

    /**
     * @var null
     */
    private $storeId = null;

    /**
     * @var Settings
     */
    private $settings;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\Blog\Helper\Url $urlHelper,
        \Amasty\Blog\Helper\Settings $settings,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->storeManagerInterface = $storeManagerInterface;
        $this->urlHelper = $urlHelper;
        $this->settings = $settings;
    }

    /**
     * @param $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        $this->setData('store_id', $storeId);

        return $this;
    }

    /**
     * @param int $page
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUrl($page = 1)
    {
        $storeId = $this->getStoreId();
        $storeId = is_array($storeId) ? $storeId[0] : $storeId;
        $baseUrl = $this->storeManagerInterface->getStore($storeId)->getBaseUrl();
        $url = $baseUrl . $this->settings->getSeoRoute();
        $postfix = $this->settings->getBlogPostfix();
        $postfix =  $page > 1 ? "{$postfix}?p={$page}" : $postfix;
        $route = $this->getRoute() ? '/' . $this->getRoute() . '/' : '';
        $urlKey = $this->getUrlKey();
        $urlKey = $urlKey && !$this->getRoute() ? '/' . $urlKey : $urlKey;

        return $url . $route . $urlKey . $postfix;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return  '';
    }

    /**
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->hasData('store_id') ? $this->getData('store_id') : $this->storeId;
    }

    /**
     * @return Settings
     */
    public function getSettingsHelper()
    {
        return $this->settings;
    }

    /**
     * @return \Amasty\Blog\Helper\Url
     */
    public function getUrlHelper()
    {
        return $this->urlHelper;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentStoreId()
    {
        return $this->storeManagerInterface->getStore()->getId();
    }
}
