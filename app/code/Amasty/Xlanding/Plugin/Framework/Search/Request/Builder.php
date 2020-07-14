<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Plugin\Framework\Search\Request;

use Magento\Framework\Search\Request\Builder as MagentoRequestBuilder;
use Magento\Store\Model\StoreManager;

class Builder
{
    const COLLECTION_PARAM_NAME = 'landing_page_id';

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Framework\EntityManager\EntityMetadataInterface
     */
    private $metadata;

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * @var \Amasty\Xlanding\Helper\Page
     */
    private $helper;

    /**
     * @var \Magento\Framework\Api\SortOrderFactory
     */
    private $sortOrderFactory;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        StoreManager $storeManager,
        \Amasty\Xlanding\Helper\Page $helper,
        \Magento\Framework\Api\SortOrderFactory $sortOrderFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->metadata = $metadataPool->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class);
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->sortOrderFactory = $sortOrderFactory;
    }

    /**
     * @param MagentoRequestBuilder $subject
     * @return array
     */
    public function beforeCreate(MagentoRequestBuilder $subject)
    {
        /**
         * @var \Amasty\Xlanding\Model\Page $page
         */
        if ($page = $this->coreRegistry->registry('amasty_xlanding_page')) {
            if ($this->helper->isAllowIndex()) {
                $subject->bind(self::COLLECTION_PARAM_NAME, $page->getId());
                if (method_exists($subject, 'setSort')) {
                    $subject->setSort(['position' => 'asc']);
                }
            } else {
                $positionData = $page->getProductPositionDataIndex($this->storeManager->getStore()->getId());
                $subject->bind($this->metadata->getLinkField(), array_keys($positionData));
            }
        }

        return [];
    }
}
