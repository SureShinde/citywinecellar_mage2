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

namespace Magestore\Rewardpoints\Helper;

/**
 * RewardPoints Name and Image Helper
 */
class Point extends \Magestore\Rewardpoints\Helper\Config
{
    const XML_PATH_POINT_NAME           = 'rewardpoints/general/point_name';
    const XML_PATH_POINT_NAME_PLURAL    = 'rewardpoints/general/point_names';
    const XML_PATH_POINT_IMAGE          = 'rewardpoints/general/point_image';

    const XML_PATH_DISPLAY_PRODUCT      = 'rewardpoints/display/product';
    const XML_PATH_DISPLAY_MINICART     = 'rewardpoints/display/minicart';

    /**
     * @var Config
     */
    protected $helper;

    /**
     * @var \Magestore\Rewardpoints\Block\Image
     */
    protected $_elementTemplate;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * Point constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param Config $globalConfig
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\View\Element\Template $elementTemplate
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magestore\Rewardpoints\Helper\Config $globalConfig,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\View\Element\Template $elementTemplate,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->helper = $globalConfig;
        $this->_elementTemplate = $elementTemplate;
        $this->_layout = $layout;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Get Label for Point, default is "Point"
     *
     * @param mixed $store
     * @return string
     */
    public function getName($store = null)
    {
        if ($pointName = trim($this->helper->getConfig(self::XML_PATH_POINT_NAME, $store))) {
            return $pointName;
        }
        return __('Point');
    }

    /**
     * Get reward Label for Points (plural), default is "Points"
     *
     * @param mixed $store
     * @return string
     */
    public function getPluralName($store = null)
    {
        if ($pluralName = trim($this->helper->getConfig(self::XML_PATH_POINT_NAME_PLURAL, $store))) {
            return $pluralName;
        }
        return __('Points');
    }

    /**
     * Get point image on store, default is template image url
     *
     * @param mixed $store
     * @return string image url
     */
    public function getImage($store = null)
    {
        if ($imgPath = trim($this->helper->getConfig(self::XML_PATH_POINT_IMAGE, $store))) {
            return $this->_storeManager->getStore()->getBaseUrl('media').'rewardpoints/'.$imgPath;
        }

        return $this->_elementTemplate->getViewFileUrl('Magestore_Rewardpoints::images/rewardpoints/point.png');
    }

    /**
     * Get Image (by HTML code)
     *
     * @param boolean $hasAnchor
     * @return string
     */
    public function getImageHtml($hasAnchor = false)
    {

        return $this->_layout->getBlockSingleton(\Magestore\Rewardpoints\Block\Image::class)
            ->setIsAnchorMode($hasAnchor)
            ->toHtml();
    }

    /**
     * Format point with unit (name). Ex: 1 Point, 2 Points
     *
     * @param int $points
     * @param mixed $store
     * @return string
     */
    public function format($points, $store = null)
    {
        $points = (int)$points;
        if (abs($points) <= 1) {
            return $points . ' ' . $this->getName($store);
        }
        return $points . ' ' . $this->getPluralName($store);
    }

    /**
     * Check show earning reward points on top link
     *
     * @param null|int|string $store
     * @return string
     */
    public function showOnProduct($store = null)
    {
        return $this->helper->getConfig(self::XML_PATH_DISPLAY_PRODUCT, $store);
    }

    /**
     * Check show earning reward points on mini cart
     *
     * @param null|int|string $store
     * @return string
     */
    public function showOnMiniCart($store = null)
    {
        return $this->helper->getConfig(self::XML_PATH_DISPLAY_MINICART, $store);
    }
}
