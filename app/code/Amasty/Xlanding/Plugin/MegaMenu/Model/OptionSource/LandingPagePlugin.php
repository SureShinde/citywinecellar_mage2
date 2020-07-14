<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Plugin\MegaMenu\Model\OptionSource;

use Amasty\Xlanding\Model\ResourceModel\Page\Collection;
use Amasty\Xlanding\Model\ResourceModel\Page\CollectionFactory;

/**
 * Class LandingPagePlugin
 */
class LandingPagePlugin
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return Collection
     */
    public function afterGetLandingPages()
    {
        return $this->collectionFactory->create();
    }
}
