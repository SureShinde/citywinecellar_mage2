<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\Page\Product;

use Magento\Store\Model\StoreManagerInterface;

abstract class AbstractDataProvider extends \Magento\Framework\Model\AbstractModel
{
    const DEFAULT_PRODUCT = 0;
    const DEFAULT_REQUEST_NAME = 'catalog_view_container';
    const DEFAULT_REQUEST_LIMIT = 0;

    /**
     * @var \Magento\Config\Model\Config
     */
    protected $backendConfig;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Amasty\Xlanding\Model\ResourceModel\Page\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var Sorting
     */
    protected $sorting;

    /**
     * @var \Amasty\Xlanding\Model\PageFactory
     */
    protected $pageFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $emulation;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Search\Request\Config
     */
    protected $searchRequestConfig;

    /**
     * @var string
     */
    protected $productIdLink;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Config\Model\Config $backendConfig,
        \Magento\Backend\Model\Session $session,
        \Amasty\Xlanding\Model\ResourceModel\Page\Product\CollectionFactory $productCollectionFactory,
        \Amasty\Xlanding\Model\Page\Product\Sorting $sorting,
        \Amasty\Xlanding\Model\PageFactory $pageFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Store\Model\App\Emulation $emulation,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Search\Request\Config $searchRequestConfig,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->session = $session;
        $this->backendConfig = $backendConfig;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->sorting = $sorting;
        $this->pageFactory = $pageFactory;
        $this->moduleManager = $moduleManager;
        $this->emulation = $emulation;
        $this->storeManager = $storeManager;
        $this->searchRequestConfig = $searchRequestConfig;
        $this->productIdLink = $productMetadata->getEdition() != 'Community' ? 'row_id' : 'entity_id';
    }

    /**
     * @return int
     */
    protected function getRuleCollectionLimit()
    {
        $requestData = $this->searchRequestConfig->get(self::DEFAULT_REQUEST_NAME);

        return isset($requestData['size']) ? $requestData['size'] : self::DEFAULT_REQUEST_LIMIT;
    }

    /**
     * @param $rule
     * @return bool
     */
    protected function isEmptyRule($rule)
    {
        $sqlConditions = $rule->getConditions()->collectConditionSql();

        return empty($sqlConditions);
    }
}
