<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Model;

use Magento\Catalog\Api\Data\ProductInterface;

class Rule extends \Magento\Rule\Model\AbstractModel
{
    const POS_ABOVE_CART    = 0;
    const POS_SIDEBAR_RIGHT = 1;
    const POS_SIDEBAR_LEFT  = 2;
    const POS_PROD_PAGE     = 3;
    const POS_CATEGORY_PAGE = 4;
    const POS_CATEGORY_PAGE_BOTTOM = 5;
    const POS_PROD_PAGE_BOTTOM = 6;
    const POS_PROD_PAGE_BELOW_CART = 7;
    const POS_CHECKOUT_BELOW_TOTAL = 8;
    const POS_CATALOG_SEARCH_TOP = 9;
    const POS_TOP_PAGE = 10;
    const POS_TOP_INDEX = 11;

    const POS_PROD_PAGE_RIGHT = 12;
    const POS_PROD_PAGE_LEFT = 13;
    const POS_AMONG_PRODUCTS = 14;
    const POS_CATEGORY_PAGE_BELOW_ADD_TO_CART = 15;

    /*
     * Display Types
     */
    const TYPE_IMAGE = 'image';
    const TYPE_CMS = 'cms';
    const TYPE_HTML = 'html';

    const SALES_RULE_PRODUCT_CONDITION_NAMESPACE = Magento\SalesRule\Model\Rule\Condition\Product::class;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    protected $serializer;

    /**
     * @var SalesRule\Condition\CombineFactory
     */
    private $combineFactory;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory
     */
    private $condProdCombineFactory;

    protected function _construct()
    {
        $this->_init(\Amasty\PromoBanners\Model\ResourceModel\Rule::class);
    }

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Backend\Helper\Js $jsHelper,
        \Amasty\Base\Model\Serializer $serializer,
        \Amasty\PromoBanners\Model\SalesRule\Condition\CombineFactory $combineFactory,
        \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $condProdCombineFactory,
        array $data = []
    ) {
        $this->jsHelper = $jsHelper;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            null,
            null,
            $data
        );

        $this->serializer = $serializer;
        $this->combineFactory = $combineFactory;
        $this->condProdCombineFactory = $condProdCombineFactory;
    }

    /**
     * @return \Amasty\PromoBanners\Model\SalesRule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->combineFactory->create();
    }

    /**
     * @return \Magento\SalesRule\Model\Rule\Condition\Product\Combine
     */
    public function getActionsInstance()
    {
        return $this->condProdCombineFactory->create();
    }

    /**
     * @param $productIds
     *
     * @return $this
     */
    public function assignProducts($productIds)
    {
        /** @var \Amasty\PromoBanners\Model\ResourceModel\Rule $resource */
        $resource = $this->getResource();
        $resource->assignProducts($this->getId(), $productIds);

        return $this;
    }

    /**
     * @param $ruleId
     *
     * @return array
     */
    public function getProducts($ruleId)
    {
        /** @var \Amasty\PromoBanners\Model\ResourceModel\Rule $resource */
        $resource = $this->getResource();

        return $resource->getProducts($ruleId);
    }
    
    /**
     * @return $this
     */
    public function afterSave()
    {
        $ids = $this->getAllowedProducts();

        if ($ids !== null && $ids) {
            $ids = $this->jsHelper->decodeGridSerializedInput($ids);
            $this->assignProducts($ids);
        }

        //Saving attributes used in rule
        $ruleProductAttributes = array_merge(
            $this->_getUsedAttributes($this->getConditionsSerialized()),
            $this->_getUsedAttributes($this->getActionsSerialized())
        );
        
        if (count($ruleProductAttributes)) {
            $this->getResource()->saveAttributes($this->getId(), $ruleProductAttributes);
        }

        return parent::afterSave();
    }

    /**
     * Return all product attributes used on serialized action or condition
     *
     * @param string $serializedString
     * @return array
     */
    protected function _getUsedAttributes($serializedString)
    {
        $result = [];
        $serializedString = $this->serializer->unserialize($serializedString);

        if (is_array($serializedString) && array_key_exists('conditions', $serializedString)) {
            $result = $this->recursiveFindAttributes($serializedString);
        }

        return array_filter($result);
    }

    /**
     * @param $serializedString
     * @return array
     */
    protected function recursiveFindAttributes($serializedString)
    {
        $arrayIterator = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($serializedString)
        );

        $result = [];
        $conditionAttribute = false;

        foreach ($arrayIterator as $key => $value) {
            if ($key == 'type' && $value == self::SALES_RULE_PRODUCT_CONDITION_NAMESPACE) {
                $conditionAttribute = true;
            }

            if ($key == 'attribute' && $conditionAttribute) {
                $result[] = $value;
                $conditionAttribute = false;
            }
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getAfterProductRow()
    {
        return (int)$this->getData('after_n_product_row') > 0
               ? (int)$this->getData('after_n_product_row') - 1
               : 0;
    }

    /**
     * @param $columnCount
     *
     * @return int
     */
    public function getAfterProductNum($columnCount)
    {
        $rowNum = $this->getAfterProductRow();
        $afterProduct = ($rowNum * $columnCount - 1);

        return $afterProduct;
    }

    /**
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function validateProduct(ProductInterface $product)
    {
        $cats = trim($this->getCats());
        
        if (!empty($cats)) {
            $cats = explode(',', $cats);
            $categoryIds = $product->getCategoryIds();

            if (is_array($categoryIds)) {
                $exist = false;
                
                foreach ($cats as $id) {
                    if (in_array($id, $categoryIds)) {
                        $exist = true;
                        break;
                    }
                }

                if (!$exist) {
                    return false;
                }
            }
        }
        
        $product->setProductId($product->getId());
        $product->setProduct($product);

        return $this->getActions()->validate($product);
    }

    /**
     * @param $query
     *
     * @return bool
     */
    public function validateSearch($query)
    {
        $searchTerms = trim($this->getShowOnSearch());

        if (empty($searchTerms)) {
            return true;
        }

        $searchTerms = explode("\r\n", $searchTerms);

        return in_array(trim($query), $searchTerms);
    }

    /**
     * @return array
     */
    public function getBannerPositions()
    {
        return $this->getBannerPosition() !== null ? explode(',', $this->getBannerPosition()) : [];
    }
}
