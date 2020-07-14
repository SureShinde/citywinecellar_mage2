<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\ResourceModel\View;

/**
 * Class
 */
class Collection extends \Amasty\Blog\Model\ResourceModel\Abstracts\Collection
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\Blog\Model\View::class, \Amasty\Blog\Model\ResourceModel\View::class);
    }
}
