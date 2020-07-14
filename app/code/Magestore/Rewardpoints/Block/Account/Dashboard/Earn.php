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
 * Rewardpoints Account Dashboard Earning Policy
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
namespace Magestore\Rewardpoints\Block\Account\Dashboard;

class Earn extends \Magento\Framework\View\Element\Template
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

    /**
     * @param $point
     * @return string
     */
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

        $rate = $this->getEarningRate();
        if ($rate && $rate->getId()) {
            $canShow = true;
        } else {
            $canShow = false;
        }
        $container = new \Magento\Framework\DataObject(array(
            'can_show' => $canShow
        ));
        $this->_eventManager->dispatch('rewardpoints_block_dashboard_earn_can_show', array(
            'container' => $container,
        ));
        return $container->getCanShow();
    }
    
    /**
     * get earning rate
     * 
     * @return Magestore_RewardPoints_Model_Rate
     */
    public function getEarningRate()
    {
        if (!$this->hasData('earning_rate')) {
            $this->setData('earning_rate',
                $this->_modelRateFactory->create()->getRate(\Magestore\Rewardpoints\Model\Rate::MONEY_TO_POINT)
            );
        }
        return $this->getData('earning_rate');
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

}
