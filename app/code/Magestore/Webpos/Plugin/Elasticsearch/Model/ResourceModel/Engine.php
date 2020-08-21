<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Plugin\Elasticsearch\Model\ResourceModel;

/**
 * Plugin resource model engine
 */
class Engine
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Engine constructor.
     *
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(\Magento\Framework\Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * After Get Allowed Visibility
     *
     * @param \Magento\Elasticsearch\Model\ResourceModel\Engine $subject
     * @param int[] $result
     * @return int[]
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetAllowedVisibility(\Magento\Elasticsearch\Model\ResourceModel\Engine $subject, $result)
    {
        if ($this->registry->registry('webpos_productsearch_fulltext')) {
            $result = array_merge($result, [\Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE]);
        }
        return $result;
    }
}
