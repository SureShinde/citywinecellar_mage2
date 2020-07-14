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
 * Rewardpoints Account Dashboard Policy
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
namespace Magestore\Rewardpoints\Block\Account\Dashboard;

class Policy extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magestore\Rewardpoints\Helper\Config
     */
    protected $_helperConfig;

    /**
     * @var \Magestore\Rewardpoints\Helper\Point
     */
    public $_helperPoint;

    /**
     * Policy constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magestore\Rewardpoints\Helper\Config $helperConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magestore\Rewardpoints\Helper\Config $helperConfig,
        \Magestore\Rewardpoints\Helper\Point $helperPoint,
        array $data)
    {
        parent::__construct($context, $data);
        $this->_helperConfig = $helperConfig;
        $this->_helperPoint = $helperPoint;

    }

    /**
     * @return mixed
     */
    public function getConfigEarnWhenSpend(){
        return $this->_helperConfig->getConfig('rewardpoints/earning/earn_when_spend', $this->_storeManager->getStore()->getId());
    }

    /**
     * @param $point
     * @return mixed
     */
    public function format($point = 0){
        return $this->_helperPoint->format($point);
    }

    /**
     * earning transaction will be expired after days
     *
     * @return int
     */
    public function getTransactionExpireDays()
    {
        $days = (int) $this->_helperConfig->getConfig(\Magestore\Rewardpoints\Helper\Calculation\Earning::XML_PATH_EARNING_EXPIRE);
        return max(0, $days);
    }
    
    /**
     * get day holling point
     * 
     * @return int
     */
    public function getHoldingDays()
    {
        $days = (int) $this->_helperConfig->getConfig(\Magestore\Rewardpoints\Helper\Calculation\Earning::XML_PATH_HOLDING_DAYS);
        return max(0, $days);
    }
    
    /**
     * Maximum point balance allowed
     * 
     * @return int
     */
    public function getMaxPointBalance()
    {
        $maxBalance = (int) $this->_helperConfig->getConfig(\Magestore\Rewardpoints\Model\Transaction::XML_PATH_MAX_BALANCE);
        return max(0, $maxBalance);
    }
    
    /**
     * Minimum point allowed to redeem
     * 
     * @return int
     */
    public function getRedeemablePoints()
    {
        $points = (int) $this->_helperConfig->getConfig(\Magestore\Rewardpoints\Helper\Customer::XML_PATH_REDEEMABLE_POINTS);
        return max(0, $points);
    }
    
    /**
     * Maximun point spneding per order
     * 
     * @return int
     */
    public function getMaxPerOrder()
    {
        $points = (int) $this->_helperConfig->getConfig(
            \Magestore\Rewardpoints\Helper\Calculation\Spending::XML_PATH_MAX_POINTS_PER_ORDER
        );
        return max(0, $points);
    }
}
