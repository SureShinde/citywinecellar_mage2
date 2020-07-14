<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider
{
    const PATH_PREFIX = 'amblog';

    const IS_ASK_EMAIL = 'comments/ask_email';

    const IS_ASK_NAME = 'comments/ask_name';

    const IS_SHOW_GDPR = 'comments/gdpr';

    const GDPR_TEXT = 'comments/gdpr_text';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    private $filterManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filter\FilterManager $filterManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->filterManager = $filterManager;
    }

    /**
     * An alias for scope config with default scope type SCOPE_STORE
     *
     * @param string $key
     * @param string|null $scopeCode
     * @param string $scopeType
     *
     * @return string|null
     */
    private function getValue($key, $scopeCode = null, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(self::PATH_PREFIX . '/' . $key, $scopeType, $scopeCode);
    }

    /**
     * @param string|null $scopeCode
     *
     * @return bool
     */
    public function isAskEmail($scopeCode = null)
    {
        return (bool)$this->getValue(self::IS_ASK_EMAIL, $scopeCode);
    }

    /**
     * @param string|null $scopeCode
     *
     * @return bool
     */
    public function isAskName($scopeCode = null)
    {
        return (bool)$this->getValue(self::IS_ASK_NAME, $scopeCode);
    }

    /**
     * @param string|null $scopeCode
     *
     * @return bool
     */
    public function isShowGdpr($scopeCode = null)
    {
        return (bool)$this->getValue(self::IS_SHOW_GDPR, $scopeCode);
    }

    /**
     * @param string|null $scopeCode
     *
     * @return string
     */
    public function getGdprText($scopeCode = null)
    {
        return $this->filterManager->stripTags(
            $this->getValue(self::GDPR_TEXT, $scopeCode),
            [
                'allowableTags' => '<a>',
                'escape' => false
            ]
        );
    }
}
