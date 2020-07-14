<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Helper;

use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Zend_Http_UserAgent
     */
    private $userAgent;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    public function __construct(
        Context $context,
        \Zend_Http_UserAgent $userAgent
    ) {
        parent::__construct($context);
        $this->userAgent = $userAgent;
        $this->request = $context->getRequest();
    }

    /**
     * @return array
     */
    public function getSocialNetworks()
    {
        return explode(
            ",",
            $this->scopeConfig->getValue(
                'amblog/social/networks',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );
    }

    /**
     * @return bool
     */
    public function isAmpEnable()
    {
        return $this->scopeConfig->isSetFlag(
            'amblog/accelerated_mobile_pages/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isCurrentPageAmp()
    {
        return $this->isAmpEnable() && strpos($this->request->getFullActionName(), '_amp_') !== false;
    }

    /**
     * @param $data
     * @param null $allowableTags
     * @param bool $allowHtmlEntities
     * @return array|string|string[]|null
     */
    public function stripTags($data, $allowableTags = null, $allowHtmlEntities = false)
    {
        $result = strip_tags($data, $allowableTags);

        return $allowHtmlEntities ? $result : $this->escapeHtml($result, $allowableTags);
    }

    /**
     * @param $data
     * @param null $allowedTags
     * @return array|string|string[]|null
     */
    private function escapeHtml($data, $allowedTags = null)
    {
        $result = [];
        if (is_array($data)) {
            foreach ($data as $item) {
                $result[] = $this->escapeHtml($item);
            }
        } else {
            $result = $this->processSingleItem($data, $allowedTags);
        }

        return $result;
    }

    /**
     * @param $data
     * @param $allowedTags
     * @return string|string[]|null
     */
    private function processSingleItem($data, $allowedTags)
    {
        if ($data) {
            if (is_array($allowedTags) && !empty($allowedTags)) {
                $allowed = implode('|', $allowedTags);
                $result = preg_replace('/<([\/\s\r\n]*)(' . $allowed . ')([\/\s\r\n]*)>/si', '##$1$2$3##', $data);
                $result = $this->htmlSpecialChars($result);
                $result = preg_replace('/##([\/\s\r\n]*)(' . $allowed . ')([\/\s\r\n]*)##/si', '<$1$2$3>', $result);
            } else {
                $result = $this->htmlSpecialChars($data);
            }
        } else {
            $result = $data;
        }

        return $result;
    }

    /**
     * @param $result
     *
     * @return string
     */
    protected function htmlSpecialChars($result)
    {
        //@codingStandardsIgnoreStart
        return htmlspecialchars($result, ENT_COMPAT, 'UTF-8', false);
        //@codingStandardsIgnoreEnd
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->_logger;
    }
}
