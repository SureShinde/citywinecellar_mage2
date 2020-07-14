<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Rewrite\Backend\Block;

/**
 * Class Menu
 * @package Magestore\ReportSuccess\Model\Rewrite\Backend\Block
 */
class Menu extends \Magento\Backend\Block\Menu
{

    /**
     * @var MenuItemChecker
     */
    protected $menuItemChecker;

    /**
     * @var AnchorRenderer
     */
    protected $anchorRenderer;

    /**
     * @var $hasReportSuccess
     */
    protected $hasReportSuccess = false;

    /**
     * @param Template\Context $context
     * @param \Magento\Backend\Model\UrlInterface $url
     * @param \Magento\Backend\Model\Menu\Filter\IteratorFactory $iteratorFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Backend\Model\Menu\Config $menuConfig
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\UrlInterface $url,
        \Magento\Backend\Model\Menu\Filter\IteratorFactory $iteratorFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Backend\Model\Menu\Config $menuConfig,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        array $data = [],
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        if(class_exists('\Magento\Backend\Block\AnchorRenderer')){
            $this->menuItemChecker =  $objectManager->create('Magento\Backend\Block\MenuItemChecker');
            $this->anchorRenderer =  $objectManager->create('Magento\Backend\Block\AnchorRenderer');
            parent::__construct($context, $url,$iteratorFactory,$authSession,$menuConfig,$localeResolver,$data,$this->menuItemChecker,$this->anchorRenderer);
        }else{
            parent::__construct($context, $url,$iteratorFactory,$authSession,$menuConfig,$localeResolver,$data);
        }
    }


    /**
     * @param \Magento\Backend\Model\Menu $menu
     * @param int $level
     * @param int $limit
     * @param array $colBrakes
     * @return string
     */
    public function renderNavigation($menu, $level = 0, $limit = 0, $colBrakes = [])
    {
        $itemPosition = 1;
        $outputStart = '<ul ' . (0 == $level ? 'id="nav" role="menubar"' : 'role="menu"') . ' >';
        $output = '';
        /** @var $menuItem \Magento\Backend\Model\Menu\Item  */
        foreach ($this->_getMenuIterator($menu) as $menuItem) {
            $menuId = $menuItem->getId();
            $itemName = substr($menuId, strrpos($menuId, '::') + 2);
            $itemClass = str_replace('_', '-', strtolower($itemName));

            $id = $this->getJsId($menuItem->getId());
            if (count($colBrakes) && $colBrakes[$itemPosition]['colbrake']) {
                $output .= '</ul></li><li class="column"><ul role="menu">';
            }

            if ((strpos($menuItem->getId(),'Magestore_ReportSuccess::') !== false) && !$this->hasReportSuccess){
                $this->hasReportSuccess = true;
                $anchorOmniChannel = '<strong class="submenu-group-title" role="presentation"><span>Omni-channel Reports</span></strong>';
                $output .=  '<li data-ui-id="menu-magestore-reportsuccess-omnichanel"
                                class="item-omnichanel  parent level-1 " role="menu-item"
                                style=" margin-bottom:0;">'.$anchorOmniChannel.'</li>';
            }
            if ((strpos($menuItem->getId(),'Magento_Reports::') !== false) && $this->hasReportSuccess ){
                $this->hasReportSuccess = false;
                $output .= '</ul></li>';
                $output .= '<hr style="border-top: dotted 1px; width:92%; color: #666; background-color: #666; height: 1px; border-color: #666; margin-top: -2em;" />';
                $output .= $this->_buildJs();
                $output .= '<li class="column" style="display: none;"><ul role="menu">';
            }

            if($id == 'menu-magestore-reportsuccess-report-listing'){
                $output .= '</ul></li>';
                $output .= '<hr style="border:0" />';
                $output .= '<li class="column"><ul role="menu">';
            }

            $subMenu = $this->_addSubMenu($menuItem, $level, $limit, $id);

            try{
                $anchor = $this->_renderAnchor($menuItem, $level);
            }catch(\Exception $e){
                $anchor = $this->anchorRenderer->renderAnchor($this->getActiveItemModel(), $menuItem, $level);
            }


                $output .= '<li ' . $this->getUiId($menuItem->getId())
                    . ' class="item-' . $itemClass . ' ' . $this->_renderItemCssClass($menuItem, $level)
                    . ($level == 0 ? '" id="' . $id . '" aria-haspopup="true' : '')
                    . '" role="menu-item">' . $anchor . $subMenu . '</li>';


            $itemPosition++;
        }

        if (count($colBrakes) && $limit) {
            $output = '<li class="column"><ul role="menu">' . $output . '</ul></li>';
        }

        return $outputStart . $output . '</ul>';
    }

    /**
     * @param \Magento\Backend\Model\Menu $items
     * @param int $limit
     * @return array|void
     */
    protected function _columnBrake($items, $limit)
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
            if ((strpos($item->getId(),'Magestore_ReportSuccess::settings') !== false)){
                $result[] = ['place' => $place, 'colbrake' => true];
            }else{
                $result[] = ['place' => $place, 'colbrake' => $colbrake];
            }
        }
        return $result;
    }

    /**
     * @return string
     */
    public function _buildJs(){
        $js = "<script type=\"text/javascript\">
                require([
                    'jquery'
                ], function($){
                        $(window).on('load', function(){
                            $('.menu-wrapper').css(\"height\",1000);
                        });
                });
                </script>";
        return $js;
    }
}