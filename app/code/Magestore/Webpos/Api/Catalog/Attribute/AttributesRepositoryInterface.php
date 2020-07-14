<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Catalog\Attribute;

/**
 * @api
 */
interface AttributesRepositoryInterface
{

    /**
     * Get list requiring attribute of custom sale attribute set
     *
     * @param int $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $attributeSetId is not found
     * @return \Magento\Catalog\Api\Data\ProductAttributeInterface[]
     */
    public function getCustomSaleRequireAttributes($attributeSetId);
}
