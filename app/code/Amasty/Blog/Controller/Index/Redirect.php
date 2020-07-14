<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Index;

/**
 * Class Redirect
 */
class Redirect extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        if ($url = $this->getRequest()->getParam('url')) {
            $this->getResponse()->setRedirect($url, 301)->sendHeaders();
        }
    }
}
