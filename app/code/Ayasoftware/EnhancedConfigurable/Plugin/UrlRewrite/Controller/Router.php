<?php
namespace Ayasoftware\EnhancedConfigurable\Plugin\UrlRewrite\Controller;

use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;

class Router
{
    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    /** @var UrlFinderInterface */
    protected $urlFinder;
    
    

    /**
     * @var ProductUrlRewriteGenerator
     */
    protected $productUrlRewriteGenerator;

    /**
     * @var UrlPersistInterface
     */
    protected $urlPersist;

    protected $objectManager;
    
    protected $_productTypeConfigurable;
    
    protected $_productloader;
    protected $customerSession;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param UrlFinderInterface $urlFinder
     * @param ProductUrlRewriteGenerator $productUrlRewriteGenerator
     * @param UrlPersistInterface $urlPersist
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        UrlFinderInterface $urlFinder,
        ProductUrlRewriteGenerator $productUrlRewriteGenerator,
        UrlPersistInterface $urlPersist,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->storeManager = $storeManager;
        $this->urlFinder = $urlFinder;
        $this->productUrlRewriteGenerator = $productUrlRewriteGenerator;
        $this->urlPersist = $urlPersist;
        $this->objectManager = $objectManager;
        $this->_productTypeConfigurable = $catalogProductTypeConfigurable;
        $this->_productloader = $_productloader;
        $this->customerSession = $customerSession;
    }

    public function aroundMatch(
        \Magento\UrlRewrite\Controller\Router $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $rewrite = $this->getRewrite($request->getPathInfo(), $this->storeManager->getStore()->getId());
        //die($request->getPathInfo());
       // if ($rewrite === null) {
            $path = trim($request->getPathInfo(), '/');
            $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $path);
            $pathInfo = explode('/', $withoutExt);
            if(isset($pathInfo[1])) {
                $path = $pathInfo[1];
            } else {
                $path = $withoutExt;
            }
            $product = $this->objectManager->create('Magento\Catalog\Model\Product')->getCollection()
                ->addAttributeToSelect('*')->addAttributeToFilter('url_key', $path)->getFirstItem();
            if($product->getId()) {
                $parentIds = $this->_productTypeConfigurable->getParentIdsByChild($product->getId());
                $customerSession = $this->objectManager->create('Magento\Customer\Model\Session');
                //print_r($parentIds);
                //exit();
                if (count($parentIds) > 0) {
                    $parent_id = $parentIds[0];
                     $customerSession->setData(
                    'ayasoftware_configurableproducts',
                    array(
                        'child_id' => $product->getId(),
                        'parent_id' => $parent_id
                    )
                );
                    $configurableProduct =$this->getLoadProduct($parent_id);
                    $childProductUrl = $this->objectManager->create('Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator')->getUrlPathWithSuffix($configurableProduct, $this->storeManager->getStore()->getId());
                    $request->setPathInfo('/' . $childProductUrl);
                } else {
                    $customerSession->setData(
                    'ayasoftware_configurableproducts',
                    array()
                );
                }
            }
       // }

        return $proceed($request);
    }

    /**
     * @param string $requestPath
     * @param int $storeId
     * @return UrlRewrite|null
     */
    protected function getRewrite($requestPath, $storeId)
    {
        return $this->urlFinder->findOneByData([
            UrlRewrite::REQUEST_PATH => trim($requestPath, '/'),
            UrlRewrite::STORE_ID => $storeId,
        ]);
    }
    
    public function getLoadProduct($id)
    {
        return $this->_productloader->create()->load($id);
    }
}