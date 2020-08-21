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

namespace Magestore\Rewardpoints\Block;

/**
 * RewardPoints Image Block
 */
class Image extends \Magento\Framework\View\Element\Template
{
    /**
     * @var null
     */
    protected $_rewardPointsHtml = null;

    /**
     * @var null
     */
    protected $_rewardAnchorHtml = null;

    /**
     * @var \Magestore\Rewardpoints\Helper\Policy
     */
    protected $helper;

    /**
     * @var \Magestore\Rewardpoints\Helper\Point
     */
    protected $_helperPoint;

    /**
     * Image constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magestore\Rewardpoints\Helper\Point $helperPoint
     * @param \Magestore\Rewardpoints\Helper\Policy $globalConfig
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magestore\Rewardpoints\Helper\Point $helperPoint,
        \Magestore\Rewardpoints\Helper\Policy $globalConfig
    ) {
        parent::__construct($context, []);
        $this->helper = $globalConfig;
        $this->_helperPoint = $helperPoint;
    }

    /**
     * @inheritDoc
     */
    public function _prepareLayout()
    {
        $this->setTemplate('Magestore_Rewardpoints::rewardpoints/image.phtml');
        return parent::_prepareLayout();
    }

    /**
     * Render block HTML, depend on mode of name showed
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getIsAnchorMode()) {
            if ($this->_rewardAnchorHtml === null) {
                $html = parent::_toHtml();
                if ($html) {
                    $this->_rewardAnchorHtml = $html;
                } else {
                    $this->_rewardAnchorHtml = '';
                }
            }
            return $this->_rewardAnchorHtml;
        } else {
            if ($this->_rewardPointsHtml === null) {
                $html = parent::_toHtml();
                if ($html) {
                    $this->_rewardPointsHtml = $html;
                } else {
                    $this->_rewardPointsHtml = '';
                }
            }
            return $this->_rewardPointsHtml;
        }
    }
    
    /**
     * Get Policy Link for reward points system
     *
     * @return string
     */
    public function getPolicyUrl()
    {
        return $this->helper->getPolicyUrl();
    }

    /**
     * Get Point Image
     *
     * @return string
     */
    public function getPointImage()
    {
        return $this->_helperPoint->getImage();
    }
}
