<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Plugin\Catalog\Product;

/**
 * Class Type
 *
 * Used to hide option custom sale
 */
class Type extends \Magento\Catalog\Model\Product\Type
{
    /**
     * After get option array
     *
     * @param \Magento\Catalog\Model\Product\Type $subject
     * @param array $result
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetOptionArray(\Magento\Catalog\Model\Product\Type $subject, $result)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get(\Magento\Framework\App\RequestInterface::class);
        if ($request->getParam('skipPluginHideCustomSaleType')) {
            return $result;
        }
        if (isset($result[\Magestore\Webpos\Helper\Product\CustomSale::TYPE])) {
            unset($result[\Magestore\Webpos\Helper\Product\CustomSale::TYPE]);
        }
        return $result;
    }
}
