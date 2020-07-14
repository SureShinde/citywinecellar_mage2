<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Controller\Index;

/**
 * Class Index
 * @package Magestore\DropshipSuccess\Controller\Index
 */
class Index extends \Magestore\DropshipSuccess\Controller\AbstractSupplier
{

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        return $this->_forward('index', 'supplier', 'dropship');
    }
}
