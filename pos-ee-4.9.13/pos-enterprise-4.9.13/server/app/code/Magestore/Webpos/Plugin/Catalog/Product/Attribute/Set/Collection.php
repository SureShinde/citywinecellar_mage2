<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Plugin\Catalog\Product\Attribute\Set;

/**
 * Class Collection
 * @package Magestore\Webpos\Plugin\Catalog\Product\Attribute\Set
 */
class Collection
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Collection constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    ){
        $this->request = $request;
    }

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $subject
     * @param $typeId
     * @return array
     */
    public function beforeSetEntityTypeFilter(\Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $subject, $typeId)
    {
        if ($this->request->getModuleName() == 'catalog') {
            $subject->addFieldToFilter('attribute_set_name', ['nin' => ['Custom_Sale_Attribute_Set']]);
        }
        return [$typeId];
    }
}