<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Plugin\XmlSitemap\Model;

use Amasty\XmlSitemap\Model\Sitemap as NativeSitemap;

class Sitemap
{
    /**
     * @var \Amasty\Xlanding\Model\ResourceModel\PageFactory
     */
    private $pageFactory;

    public function __construct(
        \Amasty\Xlanding\Model\ResourceModel\PageFactory $pageFactory
    ) {
        $this->pageFactory = $pageFactory;
    }

    /**
     * @param NativeSitemap $subgect
     * @param \Closure $proceed
     * @param $storeId
     * @return array
     */
    public function aroundGetLandingPageCollection(NativeSitemap $subgect, \Closure $proceed, $storeId)
    {
        /** @var \Amasty\Xlanding\Model\ResourceModel\Page $pageResource */
        $pageResource = $this->pageFactory->create();
        $landingPages = $pageResource->getSitemapCollection($storeId);

        return $landingPages;
    }
}
