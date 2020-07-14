<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Plugin\ElasticSearch\Plugin\Framework\Search\Request;

use Amasty\ElasticSearch\Model\Config;
use Magento\CatalogSearch\Model\ResourceModel\EngineProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\CatalogInventory\Model\Stock;

class Builder
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
    }

    /**
     * @param $subject
     * @param \Closure $proceed
     * @param $argument
	
     * @return array
     */
    public function aroundBeforeCreate($subject, \Closure $proceed, $argument)
    {
        if ($this->request->getModuleName() !== 'amasty_xlanding'
            && $this->scopeConfig->getValue(EngineProvider::CONFIG_ENGINE_PATH) == Config::ELASTIC_SEARCH_ENGINE
            && !$this->scopeConfig->isSetFlag('cataloginventory/options/show_out_of_stock')
        ) {
            $argument->bind('stock_status', Stock::STOCK_IN_STOCK);
        }

        return [];
    }
}
