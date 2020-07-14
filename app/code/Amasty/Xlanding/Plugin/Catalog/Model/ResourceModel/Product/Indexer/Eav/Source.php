<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Plugin\Catalog\Model\ResourceModel\Product\Indexer\Eav;

use Magento\Catalog\Model\ResourceModel\Product\Indexer\Eav\Source as MagentoEavSource;
use Amasty\Xlanding\Model\ResourceModel\Product\Indexer\Eav\Adapter;

class Source
{
    /**
     * @var array
     */
    private $entityIds = [];

    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    public function __construct(
        Adapter $adapter,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->adapter = $adapter;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param MagentoEavSource $subject
     * @param array $processIds
     * @return array
     */
    public function beforeReindexEntities(MagentoEavSource $subject, $processIds)
    {
        $this->entityIds = is_array($processIds) ? $processIds : [$processIds];
        return [$processIds];
    }

    /**
     * @param MagentoEavSource $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterReindexEntities(MagentoEavSource $subject, $result)
    {
        if (!$this->moduleManager->isEnabled('Amasty_VisualMerch')) {
            $this->adapter->reindexEntitiesExtended($this->entityIds);
        }
        return $result;
    }

    /**
     * @param MagentoEavSource $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterReindexAll(MagentoEavSource $subject, $result)
    {
        if (!$this->moduleManager->isEnabled('Amasty_VisualMerch')) {
            $this->adapter->reindexEntitiesExtended();
        }
        return $result;
    }
}
