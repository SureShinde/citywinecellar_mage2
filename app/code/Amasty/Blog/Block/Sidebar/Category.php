<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Sidebar;

class Category extends AbstractClass
{
    const DEFAULT_LEVEL = 1;

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("Amasty_Blog::sidebar/categories.phtml");
        $this->addAmpTemplate('Amasty_Blog::amp/sidebar/categories.phtml');
        $this->setRoute('use_categories');
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getBlockHeader()
    {
        if (!$this->hasData('header_text')) {
            $this->setData('header_text', __('Categories'));
        }

        return $this->getData('header_text');
    }

    /**
     * @return string
     */
    public function renderCategoriesTreeHtml()
    {
        return $this->getLayout()
            ->createBlock(\Amasty\Blog\Block\Sidebar\Category\TreeRenderer::class)
            ->setCategoriesLimit($this->getCategoriesLimit())
            ->render();
    }

    /**
     * @return int
     */
    public function getDefaultLevel()
    {
        return self::DEFAULT_LEVEL;
    }

    /**
     * @return int
     */
    public function getCategoriesLimit()
    {
        return (int)$this->getData('categories_limit');
    }
}
