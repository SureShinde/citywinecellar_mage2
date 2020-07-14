<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


declare(strict_types=1);

namespace Amasty\Xlanding\Model;

use Amasty\Base\Model\ConfigProviderAbstract;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider extends ConfigProviderAbstract
{
    const CONFIG_PATH_SEARCH_ENGINE = 'catalog/search/engine';

    /**
     * @var string
     */
    protected $pathPrefix = 'amasty_xlanding/';

    /**
     * @return bool
     */
    public function isElasticSearchEnabled(): bool
    {
        return strpos($this->getSearchEngine(), 'elast') !== false;
    }

    /**
     * @return string
     */
    public function getSearchEngine(): string
    {
        return (string) $this->scopeConfig->getValue(self::CONFIG_PATH_SEARCH_ENGINE, ScopeInterface::SCOPE_STORE);
    }
}
