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
 * Rewardpoints Account Dashboard Spending
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
namespace Magestore\Rewardpoints\Block\Account\Dashboard;

class Spend extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magestore\Rewardpoints\Model\RateFactory
     */
    protected $_modelRateFactory;

    /**
     * @var \Magestore\Rewardpoints\Helper\Point
     */
    public $_helperPoint;

    /**
     * @var \Magestore\Rewardpoints\Helper\Data
     */
    protected $_helperData;

    /**
     * Spend constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magestore\Rewardpoints\Model\Rate $modelRate
     * @param \Magestore\Rewardpoints\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magestore\Rewardpoints\Model\RateFactory $modelRateFactory,
        \Magestore\Rewardpoints\Helper\Data $helperData,
        \Magestore\Rewardpoints\Helper\Point $helperPoint,
        array $data)
    {
        parent::__construct($context, $data);
        $this->_modelRateFactory = $modelRateFactory;
        $this->_helperData = $helperData;
        $this->_helperPoint = $helperPoint;
    }

    public function isEnablePolicy(){
        return $this->_helperData->isEnablePolicy();
    }

    public function format($point){
        return $this->_helperPoint->format($point);
    }

    /**
     * check showing container
     * 
     * @return boolean
     */
    public function getCanShow()
    {
        $rate = $this->getSpendingRate();
        if ($rate && $rate->getId()) {
            $canShow = true;
        } else {
            $canShow = false;
        }
        $container = new \Magento\Framework\DataObject(array(
            'can_show' => $canShow
        ));
        $this->_eventManager->dispatch('rewardpoints_block_dashboard_spend_can_show', array(
            'container' => $container,
        ));
        return $container->getCanShow();
    }
    
    /**
     * get spending rate
     * 
     * @return Magestore_RewardPoints_Model_Rate
     */
    public function getSpendingRate()
    {
        if (!$this->hasData('spending_rate')) {
            $this->setData('spending_rate',
                $this->_modelRateFactory->create()->getRate(\Magestore\Rewardpoints\Model\Rate::POINT_TO_MONEY)
            );
        }
        return $this->getData('spending_rate');
    }
    
    /**
     * get current money formated of rate
     * 
     * @param Magestore_RewardPoints_Model_Rate $rate
     * @return string
     */
    public function getCurrentMoney($rate)
    {
        if ($rate && $rate->getId()) {
            $money = $rate->getMoney();
            return  $this->_helperData->convertAndFormat($money, true);
        }
        return '';
    }
    
    public function getRewardPolicyLink(){
        $link = '<a href="'.$this->_helperData->getPolicyLink().'" class="rewardpoints-title-link">'.__('Reward Policy').'</a>';
        return $link;
    }
}
