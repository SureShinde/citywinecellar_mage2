<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Plugin\Backend\Block;

use Magento\Backend\Block\AnchorRenderer;

/**
 * Class Menu
 * @package Magestore\ReportSuccess\Plugin\Backend\Block
 */
class Menu extends \Magento\Backend\Block\Menu
{

    /**
     * @var $hasReportSuccess
     */
    protected $hasReportSuccess = false;


    /**
     * @var AnchorRenderer
     */
    protected $anchorRenderer;

    /**
     * @param \Magento\Backend\Block\Menu $subject
     * @param callable $proceed
     * @param $menu
     * @param int $level
     * @param int $limit
     * @param array $colBrakes
     * @return string
     */
    public function aroundRenderNavigation(\Magento\Backend\Block\Menu $subject, callable $proceed, $menu, $level = 0, $limit = 0, $colBrakes = [])
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        if (class_exists('\Magento\Backend\Block\AnchorRenderer')) {
            $this->anchorRenderer = $objectManager->create('Magento\Backend\Block\AnchorRenderer');
        }

        $itemPosition = 1;
        $outputStart = '<ul ' . (0 == $level ? 'id="nav" role="menubar"' : 'role="menu"') . ' >';
        $output = '';
        /** @var $menuItem \Magento\Backend\Model\Menu\Item */
        foreach ($this->_getMenuIterator($menu) as $menuItem) {
            $menuId = $menuItem->getId();
            $itemName = substr($menuId, strrpos($menuId, '::') + 2);
            $itemClass = str_replace('_', '-', strtolower($itemName));

            $id = $this->getJsId($menuItem->getId());
            if (!empty($colBrakes) && count($colBrakes) && $colBrakes[$itemPosition]['colbrake']) {
                $output .= '</ul></li><li class="column"><ul role="menu">';
            }

            if ((strpos($menuItem->getId(), 'Magestore_ReportSuccess::') !== false) && !$this->hasReportSuccess) {
                $this->hasReportSuccess = true;
                $title = __('Omni-channel Reports');
                $anchorOmniChannel = '<strong class="submenu-group-title" role="presentation"><span>' . $title . '</span></strong>';
                $output .= '<li data-ui-id="menu-magestore-reportsuccess-omnichanel"
                                class="item-omnichanel  parent level-1 " role="menu-item"
                                style=" margin-bottom:0;">' . $anchorOmniChannel . '</li>';
                $output .= $this->_buildCss();
                $output .= $this->_buildHtmlTag();
            }
            if ((strpos($menuItem->getId(), 'Magento_Reports::') !== false) && $this->hasReportSuccess) {
                $this->hasReportSuccess = false;
                $output .= '</ul></li>';
                $output .= '<hr style="border-top: dotted 0px;  margin-left:30px; width:92%; border-color: #736963; margin-top: -2em;" />';
                $output .= $this->_buildJs();
                if (class_exists('\Magento\Backend\Block\AnchorRenderer')) {
                    $output .= '<li class="column" style="display: none;"><ul role="menu">';
                } else {
                    $output .= '<li class="column"><ul role="menu">';
                }
            }

            if ($id == 'menu-magestore-reportsuccess-report-listing') {
                $output .= $this->_buildHtmlTag();
            }

            $subMenu = $this->_addSubMenu($menuItem, $level, $limit, $id);

            try {
                $anchor = $this->_renderAnchor($menuItem, $level);
            } catch (\Exception $e) {
                $anchor = $this->anchorRenderer->renderAnchor($this->getActiveItemModel(), $menuItem, $level);
            }
            if ($this->checkShowMenuItem($menuItem->getId())) {
                $output .= '<li ' . $this->getUiId($menuItem->getId())
                    . ' class="item-' . $itemClass . ' ' . $this->_renderItemCssClass($menuItem, $level)
                    . ($level == 0 ? '" id="' . $id . '" aria-haspopup="true' : '')
                    . '" role="menu-item">' . $anchor . $subMenu . '</li>';
                $itemPosition++;
            }
        }

        if (!empty($colBrakes) && count($colBrakes) && $limit) {
            $output = '<li class="column"><ul role="menu">' . $output . '</ul></li>';
        }

        return $outputStart . $output . '</ul>';
    }

    /**
     * Add sub menu HTML code for current menu item
     *
     * @param \Magento\Backend\Model\Menu\Item $menuItem
     * @param int $level
     * @param int $limit
     * @param $id int
     * @return string HTML code
     */
    public function _addSubMenu($menuItem, $level, $limit, $id = null)
    {
        $output = '';
        if (!$menuItem->hasChildren()) {
            return $output;
        }
        $output .= '<div class="submenu"' . ($level == 0 && isset($id) ? ' aria-labelledby="' . $id . '"' : '') . '>';
        $colStops = null;
        if ($level == 0 && $limit) {
            $colStops = $this->_columnBrake($menuItem->getChildren(), $limit);
            $output .= '<strong class="submenu-title">' . $this->_getAnchorLabel($menuItem) . '</strong>';
            $output .= '<a href="#" class="action-close _close" data-role="close-submenu"></a>';
        }

        $output .= $this->renderNavigation($menuItem->getChildren(), $level + 1, $limit, $colStops);
        $output .= '</div>';
        return $output;
    }


    /**
     * @param \Magento\Backend\Model\Menu $items
     * @param int $limit
     * @return array|void
     */
    public function _columnBrake($items, $limit)
    {
        $total = $this->_countItems($items);
        if ($total <= $limit) {
            return;
        }
        $result[] = ['total' => $total, 'max' => ceil($total / ceil($total / $limit))];
        $count = 0;
        foreach ($items as $item) {
            $place = $this->_countItems($item->getChildren()) + 1;
            $count += $place;
            if ($place - $result[0]['max'] > $limit - $result[0]['max']) {
                $colbrake = true;
                $count = 0;
            } elseif ($count - $result[0]['max'] > $limit - $result[0]['max']) {
                $colbrake = true;
                $count = $place;
            } else {
                $colbrake = false;
            }
            if ((strpos($item->getId(), 'Magestore_ReportSuccess::settings') !== false)) {
                $result[] = ['place' => $place, 'colbrake' => true];
            } else {
                $result[] = ['place' => $place, 'colbrake' => $colbrake];
            }
        }
        return $result;
    }

    /**
     * build js
     */
    public function _buildJs()
    {
        $js = "<script type=\"text/javascript\">
                require([
                    'jquery'
                ], function($){
                        $(window).on('load', function(){
                            var menuHeight = $('.menu-wrapper').height();
                            var winHeight = $(window).height();
                            var pageHeight = $('.page-wrapper').height();
                            if(menuHeight < winHeight){
                                $('.menu-wrapper').css(\"height\",winHeight + 200);
                            }
                            if(menuHeight < pageHeight && winHeight < pageHeight){
                                $('.menu-wrapper').css(\"height\",pageHeight + 200);
                            }
                            $(\"li\").each(function( index ) {
                              if(!$(this).text()){
                                 $(this).css(\"display\",\"none\");
                              }
                            });
                        });
                });
                </script>";
        return $js;
    }

    /**
     * build Css
     */
    public function _buildCss()
    {
        $css = "<style>
                .submenu-group-title {
                        display: block !important;
                }
                </style>";
        return $css;
    }

    /**
     * build Html tag
     */
    public function _buildHtmlTag()
    {
        $output = '</ul></li>';
        $output .= '<hr style="border:0" />';
        $output .= '<li class="column"><ul role="menu">';
        return $output;
    }

    /**
     * Check show menu item Stock by location
     *
     * @return bool
     */
    public function checkShowMenuItem($itemId)
    {
        $check = true;
        switch ($itemId){
            case "Magestore_ReportSuccess::stock_by_location":
                $check =  $this->checkHasRoleResource('Magestore_ReportSuccess::inventory')
                    && !$this->checkInstalledModule("Magestore_StockManagementSuccess")
                    && $this->checkInstalledModule("Magestore_PurchaseOrderSuccess");
                break;
            case "Magestore_ReportSuccess::incomming_stock":
                $check =  $this->checkHasRoleResource('Magestore_ReportSuccess::inventory')
                    && $this->checkInstalledModule("Magestore_PurchaseOrderSuccess");
                break;
        }
        return $check;
    }


    /**
     * Check inventory reports permission
     *
     * @param $resourceId
     * @return mixed
     */
    public function checkHasRoleResource($resourceId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $autherization = $objectManager->create('\Magento\Framework\AuthorizationInterface');
        return $autherization->isAllowed($resourceId);
    }

    /**
     * Check module installed
     *
     * @param $moduleName
     * @return mixed
     */
    public function checkInstalledModule($moduleName)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $moduleManager = $objectManager->create("\Magento\Framework\Module\Manager");
        return $moduleManager->isEnabled($moduleName);
    }

}