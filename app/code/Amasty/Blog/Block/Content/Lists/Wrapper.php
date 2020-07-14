<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Content\Lists;

/**
 * Class
 */
class Wrapper extends \Magento\Framework\View\Element\Template
{
    /**
     * @return bool|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getPager()
    {
        return $this->getLayout()->getBlock(\Amasty\Blog\Block\Content\Lists::PAGER_BLOCK_NAME);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getNextUrl()
    {
        $result = '';
        $pager = $this->getPager();
        if ($pager && !$pager->isLastPage()) {
            $result = $pager->getNextPageUrl();
        }

        return $result;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPreviousUrl()
    {
        $result = '';
        $pager = $this->getPager();
        if ($pager && !$pager->isFirstPage()) {
            $result = $pager->getPreviousPageUrl();
        }

        return $result;
    }
}
