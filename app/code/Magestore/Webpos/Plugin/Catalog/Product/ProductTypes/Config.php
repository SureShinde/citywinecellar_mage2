<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Plugin\Catalog\Product\ProductTypes;

/**
 * Class Config
 *
 * Used to hide option custom sale
 */
class Config extends \Magento\Catalog\Model\ProductTypes\Config
{
    /**
     * After get all
     *
     * @param \Magento\Catalog\Model\ProductTypes\Config $subject
     * @param array $result
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetAll(
        \Magento\Catalog\Model\ProductTypes\Config $subject,
        $result
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get(\Magento\Framework\App\RequestInterface::class);
        if (!$request->getParam('skipPluginHideCustomSaleType')) {
            return $result;
        }
        if (isset($result[\Magestore\Webpos\Helper\Product\CustomSale::TYPE])) {
            unset($result[\Magestore\Webpos\Helper\Product\CustomSale::TYPE]);
        }
        return $result;
    }
}
