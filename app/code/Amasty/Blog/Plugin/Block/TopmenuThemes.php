<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Plugin\Block;

class TopmenuThemes extends Topmenu
{
    /**
     * @param $subject
     * @param $html
     * @return string
     */
    public function afterRenderCategoriesMenuHtml($subject, $html)
    {
        if ($this->link->showInNavMenu()) {
            $html .= $this->generateHtml();
        }

        return $html;
    }

    /**
     * @param $subject
     * @param $html
     * @return string
     */
    public function afterGetMegamenuHtml($subject, $html)
    {
        return $this->afterRenderCategoriesMenuHtml($subject, $html);
    }

    /**
     * @return string
     */
    private function generateHtml()
    {
        return sprintf(
            '<li class="nav-item nav-item--blog level0 level-top" id="navmenu_blog">
                    <a class="level-top" href="%s"><span>%s</span></a>
                </li>',
            $this->urlResolver->getBlogUrl(),
            $this->link->getLabel()
        );
    }
}
