<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\Page\Product;

use Amasty\Xlanding\Model\Page;
use Amasty\Xlanding\Model\ResourceModel\Page\Product\Collection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;

class IndexDataProvider extends AbstractDataProvider
{
    /**
     * @var Page
     */
    private $page;

    /**
     * @param $page
     * @param $storeId
     * @param array $productIds
     * @return array
     * @throws \Exception
     */
    public function getProductPositionData($page, $storeId, $productIds = [])
    {
        $this->setStoreId($storeId);
        $this->startEnvironmenEmulation($storeId);
        $this->initPage($page);
        $ruleCollection = $this->getRuleCollection($productIds);
        $positionData = array_flip($this->getPage()->getProductPositionData($this->getStoreId()));
        $allIds = $ruleCollection->getProductInfoByIds($positionData);
        $this->stopEnvironmentEmulation();

        return array_flip($allIds);
    }

    /**
     * @param $storeId
     * @return $this
     */
    public function startEnvironmenEmulation($storeId)
    {
        $this->emulation->startEnvironmentEmulation(
            $storeId,
            \Magento\Framework\App\Area::AREA_FRONTEND,
            true
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function stopEnvironmentEmulation()
    {
        $this->emulation->stopEnvironmentEmulation();

        return $this;
    }

    /**
     * @param $page
     * @param $product
     * @param $storeId
     * @return bool
     */
    public function validateProductByPage($page, $product, $storeId)
    {
        $this->startEnvironmenEmulation($storeId);
        $validationResult = $page->getRule()->validate($product);
        $this->stopEnvironmentEmulation();
        return !!$validationResult;
    }

    /**
     * @param array $productIds
     * @return Collection
     */
    private function getRuleCollection($productIds = [])
    {
        $collection = $this->productCollectionFactory->create();
        $page = $this->getPage();
        $page->applyAttributesFilter($collection->getSelect());
        if ($this->isEmptyRule($page->getRule())) {
            $collection->addAttributeToFilter('entity_id', self::DEFAULT_PRODUCT);
        }
        if ($this->getRuleCollectionLimit()) {
            $collection->getSelect()->limit($this->getRuleCollectionLimit());
        }
        if ($productIds) {
            $collection->addIdFilter($productIds);
        }
        $this->setCollectionOrder($collection);
        return $collection;
    }

    /**
     * @param $collection
     * @return $this
     */
    private function setCollectionOrder($collection)
    {
        $this->sorting->applySorting($collection, $this->getPage()->getSortOrder());
        return $this;
    }

    /**
     * @param Page $page
     * @throws \Exception
     * @return $this
     */
    private function initPage(Page $page)
    {
        if (!$page->getId()) {
            throw new LocalizedException(__('Requested page does not exist'));
        }

        if (!$page->getRule()->getId()) {
            throw new LocalizedException(__('Rule for this page does not exist'));
        }
        $this->page = $page;

        return $this;
    }

    /**
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->setData('store_id', $storeId);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStoreId()
    {
        if (!$this->hasData('store_id')) {
            $this->setData('store_id', Store::DISTRO_STORE_ID);
        }

        return $this->getData('store_id');
    }
}
