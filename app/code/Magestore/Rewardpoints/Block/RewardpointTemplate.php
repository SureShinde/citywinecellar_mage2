<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpoints Core Block Template Block
 * You should write block extended from this block when you write plugin
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
namespace Magestore\Rewardpoints\Block;
class RewardpointTemplate extends \Magento\Framework\View\Element\Template {

    /**
    * @var \Magento\Framework\Module\Manager
    */
    protected $_moduleManager;

    /**
     * @var \Magestore\Rewardpoints\Helper\Point
     */
    public $_helperPoint;

    /**
     * @var \Magestore\Rewardpoints\Helper\Data
     */
    public $helper;

    /**
     * RewardpointTemplate constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magestore\Rewardpoints\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magestore\Rewardpoints\Helper\Data $helper,
        \Magestore\Rewardpoints\Helper\Point $helperPoint,
        array $data
    )
    {
        parent::__construct($context, $data);
        $this->_moduleManager =  $moduleManager;
        $this->helper =  $helper;
        $this->_helperPoint = $helperPoint;
    }

    public function getStoreConfig($path){
        return $this->_scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $point
     * @return string
     */
    public function format($point){
        return $this->_helperPoint->format($point);
    }

    /**
     * check reward points system is enabled or not
     *
     * @return boolean
     */
    public function isEnable() {
        return $this->helper->isEnable();
    }

    public function getPluralName(){
        return $this->_helperPoint->getPluralName();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml() {
        if ($this->isEnable()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * @param $plugin
     * @return bool
     */
    public function isPluginEnable($plugin) {
        if($this->_moduleManager->isEnabled($plugin)) {
            return true;
        }
        return false;
    }

    public function createUrl($url,$params = []){
        return $this->_urlBuilder->getDirectUrl($url,$params);
    }

}
