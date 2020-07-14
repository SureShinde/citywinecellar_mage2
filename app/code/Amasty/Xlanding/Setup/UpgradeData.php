<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */

/**
 * Copyright © 2016 Amasty. All rights reserved.
 */

namespace Amasty\Xlanding\Setup;


use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Amasty\Xlanding\Model\ResourceModel\Page\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var CollectionFactory
     */
    private $pageCollectionFactory;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var AddVirtualCategories
     */
    private $addVirtualCategories;

    public function __construct(
        CollectionFactory $pageCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        \Magento\Framework\App\State $appState,
        AddVirtualCategories $addVirtualCategories
    ) {
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->appState = $appState;
        $this->addVirtualCategories = $addVirtualCategories;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $this->appState->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_ADMINHTML,
            [$this, 'upgrateWrappwer'],
            [$setup, $context]
        );

        $setup->endSetup();
    }

    /**
     * @param ModuleContextInterface $context
     * @return $this
     */
    public function upgrateWrappwer(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.1.5', '<')) {
            $this->updateUrlRewrites();
        }

        if (version_compare($context->getVersion(), '1.2.7', '<')) {
            $this->applyIsAnchorForRootCategories();
        }

        if (version_compare($context->getVersion(), '1.4.0', '<')) {
            $this->addVirtualCategories->execute($setup);
        }

        return $this;
    }

    /**
     * Trigger excess url rewrite removal
     */
    private function updateUrlRewrites()
    {
        $this->pageCollectionFactory->create()->save();
    }

    /**
     * @return void
     */
    private function applyIsAnchorForRootCategories()
    {
        try {
            $rootCategories = $this->categoryCollectionFactory->create();
            $rootCategories
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('level', 1);
            foreach ($rootCategories as $category) {
                $category->setIsAnchor(true);
            }

            $rootCategories->save();
        } catch (\Exception $e) {
            // "Invalid attribute name: level" while running unit tests in some cases
        }
    }

}
