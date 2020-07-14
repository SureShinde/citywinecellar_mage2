<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Index;
use Magento\Backend\App\Action;

/**
 * Class Index
 * @package Magestore\Webpos\Controller
 */
class DeleteCustomSale extends \Magento\Backend\App\Action
{


    /**
     * DeleteCustomSale constructor.
     * @param Action\Context $context
     * @param \Magestore\Webpos\Helper\Product\CustomSale $customSaleHelper
     */
    public function __construct(
        Action\Context $context,
        \Magestore\Webpos\Helper\Product\CustomSale $customSaleHelper
    )
    {
        parent::__construct($context);
        $customSaleHelper->deleteProduct();
    }


    public function execute()
    {
        throw new \Exception('Deleted');
    }


}
