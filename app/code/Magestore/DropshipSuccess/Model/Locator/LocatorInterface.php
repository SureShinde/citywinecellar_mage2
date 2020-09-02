<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Model\Locator;

/**
 * Interface LocatorInterface
 * @package Magestore\DropshipSuccess\Model\Locator
 */
interface LocatorInterface
{

    /**
     * @param string
     * @return mixed
     */
    public function getSession($key);

    /**
     * @param string string
     * @return
     */
    public function setSession($key, $data);

    /**
     * @param string string
     * @return
     */
    public function unsetSession($key);
}
