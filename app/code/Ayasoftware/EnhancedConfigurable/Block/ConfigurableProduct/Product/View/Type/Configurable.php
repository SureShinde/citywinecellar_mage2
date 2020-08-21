<?php

/**
 * Copyright   2019 Ayasoftware (http://www.ayasoftware.com).
 * See COPYING.txt for license details.
 * author      EL HASSAN MATAR <support@ayasoftware.com>
 */

namespace Ayasoftware\EnhancedConfigurable\Block\ConfigurableProduct\Product\View\Type;

use Magento\Catalog\Helper\Product as CatalogProduct;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Configurable {

    protected $jsonEncoder;
    protected $jsonDecoder;
    protected $objectManager;
    protected $stockRegistry;
    protected $customerSession;

    /**
     * URL builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;
    protected $output;
    protected $helper;

    /**
     * Catalog product
     *
     * @var \Magento\Catalog\Helper\Product
     */
    protected $catalogProduct = null;
    protected $_storeManager;
    protected $_appConfigScopeConfigInterface;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeModelStoreManagerInterface;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface 
     */
    protected $_priceCurrency;

    /**
     * URL builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    public function __construct(
    EncoderInterface $jsonEncoder, 
    DecoderInterface $jsonDecoder, 
    ScopeConfigInterface $appConfigScopeConfigInterface, 
    \Magento\Framework\ObjectManagerInterface $objectManager, 
    \Ayasoftware\EnhancedConfigurable\Helper\Data $helper, 
    \Magento\Catalog\Helper\Product $catalogProduct, 
    \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry, 
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    \Magento\Framework\UrlInterface $urlBuilder, 
    \Magento\Catalog\Helper\Output $output, 
    \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency, 
    \Magento\Customer\Model\Session $customerSession
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->jsonDecoder = $jsonDecoder;
        $this->_appConfigScopeConfigInterface = $appConfigScopeConfigInterface;
        $this->objectManager = $objectManager;
        $this->helper = $helper;
        $this->catalogProduct = $catalogProduct;
        $this->stockRegistry = $stockRegistry;
        $this->_storeManager = $storeManager;
        $this->_urlBuilder = $urlBuilder;
        $this->output = $output;
        $this->_priceCurrency = $priceCurrency;
        $this->customerSession = $customerSession;
    }
    /**
     * Get Allowed Products
     * @param \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject
     * @param \Closure $proceed
     *  @return \Magento\Catalog\Model\Product[] 
     */
    public function aroundGetAllowProducts(
    \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject, \Closure $proceed
    ) {
        if( ! $subject->hasAllowProducts()) {
            $products = [];
            $skipSaleableCheck =  $this->catalogProduct->getSkipSaleableCheck();
            $allProducts = $subject->getProduct()->getTypeInstance()->getUsedProducts($subject->getProduct(), null);
            foreach($allProducts as $product) {
                if($this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/details/show_out_of_stock_products', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
                    $products[] = $product;
                } else {
                    if($product->isSaleable() || $skipSaleableCheck) {
                        $products[] = $product;
                    }
                }
            }
            $subject->setAllowProducts($products);
        }
        return $subject->getData('allow_products');
    }

    public function aroundGetJsonConfig(
    \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject, \Closure $proceed
    ) {
        $storeId = $this->getStoreId();
        $config = $this->jsonDecoder->decode($proceed());
        $productsCollection = $subject->getAllowProducts();
        $currentProduct = $subject->getProduct();
        $priceHelper = $this->objectManager->create('Magento\Framework\Pricing\Helper\Data');
        $stockInfo = array();
        $skus = array();
        $names = array();
        $descriptions = array();
        if($this->isCurrentlySecure()) {
            $mediaUrl = $this->_urlBuilder->getUrl('', array('_secure' => true));
        } else {
            $mediaUrl = $this->_urlBuilder->getUrl('');
        }
        foreach($productsCollection as $product) {
            $productId = $product->getId();
            if($this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/details/customstockdisplay', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
                $stockItem = $this->stockRegistry->getStockItem($product->getId());
                if($stockItem->getQty() <= 0 || ! ($stockItem->getIsInStock())) {
                    $stockInfo[$productId] = array(
                        "stockLabel" => __('Out of stock'),
                        "stockQty" => intval($stockItem->getQty()),
                        "is_in_stock" => false
                    );
                } else {
                    $stockInfo[$productId] = array(
                        "stockLabel" => __('In Stock'),
                        "stockQty" => intval($stockItem->getQty()),
                        "is_in_stock" => true
                    );
                }
            }
            $finalPrice = $product->getFinalPrice();
            $childProductUrl = $this->objectManager->create('Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator')->getUrlPathWithSuffix($product, $storeId);
            if($product->getCustomerGroupId()) {
                $finalPrice = $product->getGroupPrice();
            }

            if($product->getTierPrice()) {
                $tprices = array();
                foreach($tierprices = $product->getTierPrice() as $tierprice) {
                    $tprices[] = $tierprice['price'];
                }
                $tierpricing = min($tprices);
            } else {
                $tierpricing = '';
            }
            $finalPrice = $priceHelper->currency($finalPrice, false, false);

            $has_image = false;
            if($this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/details/image', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
                if($product->getData('thumbnail') && ($product->getData('thumbnail') != 'no_selection')) {
                    $has_image = true;
                }
            }
            $childProducts[$productId] = array(
                "price" => $this->_registerJsPrice($priceHelper->currency($product->getPrice(), false, false)),
                "finalPrice" => $this->_registerJsPrice($finalPrice),
                "tierpricing" => $this->_registerJsPrice($tierpricing),
                "has_image" => $has_image,
                'productUrl' => $childProductUrl,
                 "imageUrl" => $mediaUrl.'pub/media/catalog/product/'.$product->getImage()
            );

              if($this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/details/producturl', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
                  $childProducts[$productId]["urlKey"] = $this->helper->getRealUrlKey($productId);
               }
            if($this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/details/sku', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
                $skus[$productId] = array(
                    "sku" => $this->output->productAttribute($product, $product->getSku(), 'sku')
                );
            }

            if($this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/details/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
                $names[$productId] = array(
                    "name" => $this->output->productAttribute($product, $product->getName(), 'name')
                );
            }
            $productDetails = $this->helper->getProductDetails($product);
            if($this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/details/description', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
                $descriptions[$productId] = array(
                    "description" => /* $this->output->productAttribute($product, $product->getDescription(), 'description') */ $productDetails['description']
                );
            }
            if($this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/details/short_description', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
                $short_descriptions[$productId] = array(
                    "short_description" => $productDetails['short_description']
                );
            }
        }
        if($this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/settings/instocklabel', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $config['instocklabel'] = true;
        } else {
            $config['instocklabel'] = false;
        }

        if($this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/details/disable_out_of_stock_option', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $config['disable_out_of_stock_option'] = true;
        } else {
            $config['disable_out_of_stock_option'] = false;
        }

        if($this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/details/updateurl', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $config['updateurl'] = true;
        } else {
            $config['updateurl'] = false;
        }

        if($this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/settings/hideprices', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $config['hideprices'] = true;
        } else {
            $config['hideprices'] = false;
        }
        if($this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/details/short_description', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $config['short_description'] = $currentProduct->getShortDescription();
            $config['short_descriptions'] = $short_descriptions;
            $config['product_short_description_markup'] = $this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/markup/product_short_description_markup', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $config['updateShortDescription'] = true;
        } else {
            $config['updateShortDescription'] = false;
        }
        if($this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/details/description', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $config['description'] = $currentProduct->getDescription();
            $config['descriptions'] = $descriptions;
            $config['product_description_markup'] = $this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/markup/product_description_markup', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $config['updateDescription'] = true;
        } else {
            $config['updateDescription'] = false;
        }
        if($this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/details/sku', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $config['sku'] = $currentProduct->getSku();
            $config['skus'] = $skus;
            $config['product_sku_markup'] = $this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/markup/product_sku_markup', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $config['updateSku'] = true;
        } else {
            $config['updateSku'] = false;
        }
        if($this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/details/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $config['name'] = $currentProduct->getName();
            $config['names'] = $names;
            $config['product_name_markup'] = $this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/markup/product_name_markup', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $config['updateName'] = true;
        } else {
            $config['updateName'] = false;
        }
        if($this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/details/additional', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $config['additional'] = true;
            $config['product_additional_markup'] = $this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/markup/product_additional_markup', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        } else {
            $config['additional'] = false;
        }
        if($this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/details/image', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $config['image'] = true;
            $config['product_image_markup'] = $this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/markup/product_image_markup', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        } else {
            $config['product_image_markup'] = $this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/markup/product_image_markup', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $config['image'] = false;
        }
        $config['childProducts'] = $childProducts;
        $config['stockInfo'] = $stockInfo;
        $config['priceFromLabel'] = __($this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/settings/from_label', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
        if($this->isCurrentlySecure()) {
            $config['ajaxBaseUrl'] = $this->_urlBuilder->getUrl('econfig/ajax/', array('_secure' => true));
        } else {
            $config['ajaxBaseUrl'] = $this->_urlBuilder->getUrl('econfig/ajax/');
        }

        if($this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/details/customstockdisplay', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $config['customStockDisplay'] = true;
            $config['product_customstockdisplay_markup'] = $this->_appConfigScopeConfigInterface->getValue('enhanced_configurable_products/markup/product_customstockdisplay_markup', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        } else {
            $config['customStockDisplay'] = false;
        }
        return $this->jsonEncoder->encode($config);
    }

    /**
     * Replace ',' on '.' for js
     *
     * @param float $price
     * @return string
     */
    protected function _registerJsPrice($price) {
        return number_format(str_replace(',', '.', (float) $price), 2, '.', '');
    }

    public function getStoreId() {
        return $this->_storeManager->getStore()->getStoreId();
    }

    /**
     * Returns true if the store is currently secure. 
     * @return boolean
     */
    public function isCurrentlySecure() {
        return $this->_storeManager->getStore()->isCurrentlySecure();
    }

}
