<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Amp;

use Magento\Framework\View\Element\Template;

class Page extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Amasty\Blog\Model\Amp\ContentModificator
     */
    private $contentModificator;

    public function __construct(
        Template\Context $context,
        \Amasty\Blog\Model\Amp\ContentModificator $contentModificator,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->contentModificator = $contentModificator;
    }

    /**
     * @param string $alias
     * @param bool $useCache
     *
     * @return string
     */
    public function getChildHtml($alias = '', $useCache = true)
    {
        $html = parent::getChildHtml($alias, $useCache);
        if ($html && !in_array($alias, ['head'])) {
            $html = $this->contentModificator->validateHtml($html);
        }

        return $html;
    }
}
