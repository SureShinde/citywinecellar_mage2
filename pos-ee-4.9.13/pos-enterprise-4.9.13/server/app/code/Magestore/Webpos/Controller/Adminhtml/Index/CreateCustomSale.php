<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Controller\Adminhtml\Index;
use Magento\Backend\App\Action;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class Index
 * @package Magestore\Webpos\Controller
 */
class CreateCustomSale extends \Magento\Backend\App\Action
{


    /**
     * CreateCustomSale constructor.
     * @param Context $context
     * @param ModuleDataSetupInterface $setup
     * @param \Magestore\Webpos\Helper\Product\CustomSale $customSaleHelper
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        Action\Context $context,
        ModuleDataSetupInterface $setup,
        \Magestore\Webpos\Helper\Product\CustomSale $customSaleHelper
    )
    {
        parent::__construct($context);
        $customSaleHelper->createProduct($setup);
    }

    public function execute()
    {
        throw new \Exception('Created');
    }


}
