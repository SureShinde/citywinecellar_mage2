<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Block\Amp;

use Magento\Framework\DataObject;

class Topmenu extends \Magento\Theme\Block\Html\Topmenu
{
    /**
     * Add sub menu HTML code for current menu item
     *
     * @param \Magento\Framework\Data\Tree\Node $child
     * @param string $childLevel
     * @param string $childrenWrapClass
     * @param int $limit
     *
     * @return string
     */
    protected function _addSubMenu($child, $childLevel, $childrenWrapClass, $limit)
    {
        $html = '';
        if (!$child->hasChildren()) {
            $html = '<span></span>';

            return $html;
        }

        $colStops = [];
        if ($childLevel == 0 && $limit) {
            $colStops = $this->_columnBrake($child->getChildren(), $limit);
        }

        $html .= '<ul class="amblog-amp-submenu level' . $childLevel . ' ' . $childrenWrapClass . '">';
        $html .= '<li><h2 class="amblog-amp-title"><a class="amblog-amp-item -all" href="' . $child->getUrl() . '">
                  <span>' . $this->escapeHtml(__('View All %1', $child->getName())) . '</span></a></h2></li>';
        $html .= $this->getHtmlContent($child, $childrenWrapClass, $limit, $colStops);
        $html .= '</ul>';

        return $html;
    }

    /**
     * For back compatibility
     *
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string
     */
    public function getHtml($outermostClass = '', $childrenWrapClass = '', $limit = 0)
    {
        $this->_eventManager->dispatch(
            'page_block_html_topmenu_gethtml_before',
            ['menu' => $this->getMenu(), 'block' => $this, 'request' => $this->getRequest()]
        );

        $this->getMenu()->setOutermostClass($outermostClass);
        $this->getMenu()->setChildrenWrapClass($childrenWrapClass);

        $transportObject = new DataObject(
            [
                'html' => $this->getHtmlContent(
                    $this->getMenu(),
                    $childrenWrapClass,
                    $limit
                )
            ]
        );

        $this->_eventManager->dispatch(
            'page_block_html_topmenu_gethtml_after',
            ['menu' => $this->getMenu(), 'transportObject' => $transportObject]
        );

        return $transportObject->getHtml();
    }

    /**
     * Recursively generates top menu html from data that is specified in $menuTree
     *
     * @param \Magento\Framework\Data\Tree\Node $menuTree
     * @param string $childrenWrapClass
     * @param int $limit
     * @param array $colBrakes
     *
     * @return string
     */
    private function getHtmlContent(
        \Magento\Framework\Data\Tree\Node $menuTree,
        $childrenWrapClass,
        $limit,
        $colBrakes = []
    ) {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = $parentLevel === null ? 0 : $parentLevel + 1;

        $counter = 1;
        $itemPosition = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        /** @var \Magento\Framework\Data\Tree\Node $child */
        foreach ($children as $child) {
            if ($childLevel === 0 && $child->getData('is_parent_active') === false) {
                continue;
            }
            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $currentClass = $child->getClass();

                if (empty($currentClass)) {
                    $child->setClass($outermostClass);
                } else {
                    $child->setClass($currentClass . ' ' . $outermostClass);
                }
            }

            if (is_array($colBrakes) && !empty($colBrakes) && $colBrakes[$counter]['colbrake']) {
                $html .= '</amp-accordion></section><section class="column"><amp-accordion animate>';
            }

            if ($childLevel == 0) {
                $html .= '<section ';
            } else {
                $html .= '<li ';
            }
            $html .= $this->_getRenderedMenuItemAttributes($child) . '>';

            $hasChildren = $child->hasChildren();
            if ($hasChildren && $childLevel == 0) {
                $childName = $this->escapeHtml($child->getName());
                $html .= '<h2 class="amblog-amp-title"><span>' . $childName . '</span>
                            <i class="fas fa-chevron-right amblog-amp-next"></i></h2>';
            } else {
                $html .= '<h2 class="amblog-amp-title"><a class="amblog-amp-item" href="' . $child->getUrl()
                    . '"><span>' . $this->escapeHtml(
                        $child->getName()
                    ) . '</span></a></h2>';
            }

            $html .= $this->_addSubMenu(
                $child,
                $childLevel,
                $childrenWrapClass,
                $limit
            );

            if ($childLevel == 0) {
                $html .= '</section>';
            } else {
                $html .= '</li>';
            }

            $itemPosition++;
            $counter++;
        }

        if (is_array($colBrakes) && !empty($colBrakes) && $limit) {
            $html = '<section class="column"><amp-accordion animate>' . $html . '</amp-accordion></section>';
        }

        return $html;
    }

    /**
     * Override parent method
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $keyInfo = \Magento\Framework\View\Element\Template::getCacheKeyInfo();
        $keyInfo[] = 'amp';

        return $keyInfo;
    }
}
