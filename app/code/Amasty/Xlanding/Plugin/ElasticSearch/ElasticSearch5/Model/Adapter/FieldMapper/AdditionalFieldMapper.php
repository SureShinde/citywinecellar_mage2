<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Plugin\ElasticSearch\ElasticSearch5\Model\Adapter\FieldMapper;

class AdditionalFieldMapper
{
    const ES_DATA_TYPE_INTEGER = 'integer';
    const FIELD_NAME_POSITION_TEMPLATE = 'landing_page_position_%s';
    const FIELD_NAME = 'landing_page_id';

    /**
     * @var \Amasty\Xlanding\Model\ResourceModel\Page\Collection
     */
    private $pageCollection;

    public function __construct(\Amasty\Xlanding\Model\ResourceModel\Page\CollectionFactory $collectionFactory)
    {
        $this->pageCollection = $collectionFactory->create();
    }

    /**
     * @param mixed $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetAllAttributesTypes($subject, array $result)
    {
        $result[self::FIELD_NAME] = ['type' => self::ES_DATA_TYPE_INTEGER];
        foreach ($this->pageCollection as $item) {
            $result[sprintf(self::FIELD_NAME_POSITION_TEMPLATE, $item->getId())] = [
                'type' => self::ES_DATA_TYPE_INTEGER
            ];
        }
        return $result;
    }
}
