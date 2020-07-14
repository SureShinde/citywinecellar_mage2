<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Plugin\ElasticSearch\SearchAdapter\Query\Builder;

use Magento\Framework\Registry;

/**
 * Sort builder plugin
 */
class SortPlugin
{
    const FIELD_NAME_POSITION_TEMPLATE = 'landing_page_position_%s';

    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param mixed $subject
     * @param array $result
     * @return array
     */
    public function afterGetSort($subject, $result)
    {
        if ($page = $this->registry->registry('amasty_xlanding_page')) {
            foreach ($result as $sortKey => $sort) {
                $key = key($sort);
                $order = $sort[$key]['order'];
                if (strpos($key, 'category_position') === 0 || strpos($key, 'position_category') === 0) {
                     $result[$sortKey] = [
                        sprintf(self::FIELD_NAME_POSITION_TEMPLATE, $page->getId()) => [
                            'order' => strtolower($order)
                        ]
                     ];
                }
            }
        }

        return $result;
    }

    /**
     * @param mixed $subject
     * @param array $result
     * @return array
     */
    public function afterExecute($subject, $result)
    {
        return $this->afterGetSort($subject, $result);
    }
}
