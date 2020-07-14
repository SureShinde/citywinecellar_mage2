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
 * @package     Magestore_Giftvoucher
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
namespace Magestore\Rewardpoints\Controller\Checkout;

/**
 * @category Magestore
 * @package  Magestore_Affiliateplus
 * @module   Affiliateplus
 * @author   Magestore Developer
 */
class UpdateTotal extends \Magestore\Rewardpoints\Controller\AbstractAction
{
    /**
     * @return mixed
     */
    public function execute()
    {
        $this->_checkoutSessionFactory->create()->setData('use_point', true);
        $data = $this->getRequest()->getPostValue();
        $rulesData = $this->_helperSpend->getRulesData();
        if(isset($data['reward_sales_rule']) && isset($data['reward_sales_point'])){
            $usePoint = $data['reward_sales_point'];
            if(isset($data['use_max_point'])){
                if(isset($rulesData) && ($data['use_max_point'] == 1)){
                    $usePoint = $rulesData['rate']['sliderOption']['maxPoints'];
                }
                $this->_checkoutCartFactory->create()->getQuote()->setUseMaxPoint($data['use_max_point'])->save();
            }
            $this->_checkoutSessionFactory->create()->setRewardSalesRules(array(
                'rule_id' => $data['reward_sales_rule'],
                'use_point' => $usePoint,
            ));
        }
        if ($this->_checkoutCartFactory->create()->getQuote()->getItemsCount()) {
            $this->_checkoutCartFactory->create()->save();
            $this->checkUseDefault();
        }
        $this->_checkoutSessionFactory->create()->getQuote()->collectTotals();
        $amount = $this->_checkoutCartFactory->create()->getQuote()->getRewardpointsBaseDiscount();
        $result = [
            'earning' => $this->_helperPoint->format($this->_checkoutForm->getEarningPoint()),
            'spending' => $this->_helperPoint->format($this->_checkoutForm->getSpendingPoint()),
            'usePoint' =>  strip_tags($this->_helperData->convertAndFormat(-$amount)),
            'rate' => $rulesData['rate']
        ];
        return $this->getResponse()->setBody(\Zend_Json::encode($result));

    }

    public function checkUseDefault(){
        $this->_checkoutSessionFactory->create()->setData('use_max', 0);
        $rewardSalesRules = $this->_checkoutSessionFactory->create()->getRewardSalesRules();
        $arrayRules = $this->_helperSpend->getRulesArray();
        if($this->_calculationSpending->isUseMaxPointsDefault()){
            if(isset($rewardSalesRules['use_point']) &&
                isset($rewardSalesRules['rule_id']) &&
                isset($arrayRules[$rewardSalesRules['rule_id']]) &&
                isset($arrayRules[$rewardSalesRules['rule_id']]['sliderOption'])&&
                isset($arrayRules[$rewardSalesRules['rule_id']]['sliderOption']['maxPoints']) && ($rewardSalesRules['use_point'] < $arrayRules[$rewardSalesRules['rule_id']]['sliderOption']['maxPoints'])){
                $this->_checkoutSessionFactory->create()->setData('use_max', 0);
            }else{
                $this->_checkoutSessionFactory->create()->setData('use_max', 1);
            }
        }
    }

}