<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Catalog;

/**
 * Catalog Category model
 *
 * @method \Magento\Catalog\Model\ResourceModel\Category\Collection getCollection()
 */
class Category extends \Magento\Catalog\Model\Category
    implements \Magestore\Webpos\Api\Data\Catalog\CategoryInterface
{

    /** root categoty id   */
    protected $rootCategory;

    public function getRootCategoryId()
    {
        if (!$this->rootCategory) {
            $storeManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
                '\Magento\Store\Model\StoreManagerInterface'
            );
            $this->rootCategory = $storeManager->getStore()->getRootCategoryId();
        }
        return $this->rootCategory;
    }

    /**
     * Get category image
     *
     * @return string/null
     */
    public function getImage()
    {
        $storeManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
            '\Magento\Store\Model\StoreManagerInterface'
        );
        $url = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if ($this->getData('image', null)) {
            return $url. 'catalog/category/'. ltrim(str_replace('\\', '/', $this->getData('image')), '/');
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $defaultStoreId = $this->_storeManager->getStore()->getId();
        $appEmulation = $objectManager->get('\Magento\Store\Model\App\Emulation');
        $appEmulation->startEnvironmentEmulation($defaultStoreId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
        $block = $objectManager->get('\Magestore\Webpos\Block\AbstractBlock');
        $url = $block->getViewFileUrl('Magestore_Webpos::images/category/image.jpg');
        $appEmulation->stopEnvironmentEmulation();
        return $url;
    }

    /**
     * Retrieve children ids
     *
     * @return array
     */
    public function getChildrenIds()
    {
        return $this->getResource()->getChildren($this, false);
    }


    /**
     * @inheritdoc
     */
    public function getChildren($recursive = false, $isActive = true, $sortByPosition = false){
        return $this->getData('children_object');
    }

    /**
     * @inheritdoc
     */
    public function setChildren($children){
        return $this->setData('children_object', $children);
    }
    /**
     * is first category
     * @return int
     */
    public function isFirstCategory()
    {
        $rootCategoryId = $this->getRootCategoryId();
        if ($this->getParentId() == $rootCategoryId) {
            return 1;
        }
        return 0;
    }
    
}
