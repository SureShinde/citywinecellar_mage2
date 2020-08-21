<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Helper;

/**
 * Giftvoucher draw helper
 *
 * @module   Giftvoucher
 * @author   Magestore Developer
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class Drawgiftcard extends \Magestore\Giftvoucher\Helper\Data
{
    /**
     * Get the directory of gift code image
     *
     * @param string $code
     *
     * @return array
     */
    public function getImagesInFolder($code)
    {
        $directory = $this->getBaseDirMedia()->getAbsolutePath('giftvoucher/draw/' . $code . '/');
        return \Magento\Framework\Filesystem\Glob::glob($directory . $code . "*.png");
    }
}
