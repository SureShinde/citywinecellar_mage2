<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_PromoBanners
 */


namespace Amasty\PromoBanners\Model\Factories;

class SegmentFactory
{
    const CUSTOMER_ID = 'customer_id';
    const SEGMENT_ID = 'segment_id';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * SegmentFactory constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ){
        $this->objectManager = $objectManager;
    }

    /**
     * @return \Amasty\Segments\Model\ResourceModel\Segment\Collection
     */
    public function getSegmentCollection()
    {
        return $this->objectManager->create(\Amasty\Segments\Model\ResourceModel\Segment\Collection::class);
    }

    /**
     * @return \Amasty\Segments\Model\ResourceModel\Index
     */
    public function getSegmentIndex()
    {
        return $this->objectManager->create(\Amasty\Segments\Model\ResourceModel\Index::class);
    }
}
