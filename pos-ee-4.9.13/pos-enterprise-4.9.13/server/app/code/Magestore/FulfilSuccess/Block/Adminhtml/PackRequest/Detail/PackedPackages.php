<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\PackRequest\Detail;

class PackedPackages extends \Magento\Backend\Block\Template
{
    /**
     * @return \Magestore\FulfilSuccess\Api\Data\PackageInterface[]
     */
    public function getPackages()
    {
        /** @var \Magestore\FulfilSuccess\Api\Data\PackRequestInterface $packRequest */
        $packRequest = $this->getParentBlock()->getPackRequest();
        $packages = $packRequest->getPackages();

        return $packages;
    }
}