<?php
/**
 * Created by PhpStorm.
 * User: duongdiep
 * Date: 19/02/2017
 * Time: 17:08
 */

namespace Magestore\Rewardpoints\Model\Service;

class IntegrateEE extends \Magestore\Rewardpoints\Model\Service\AbstractIntegrateEE
{

    /**
     * tables
     */
    const MAGENTO_REWARD = 'magento_reward';
    const MAGENTO_REWARD_CONVERT = 'rewardpoints_customer';

    const MAGENTO_REWARD_RATE = 'magento_reward_rate';
    const MAGENTO_REWARD_RATE_CONVERT = 'rewardpoints_rate';

    const MAGENTO_REWARD_SALESRULE = 'magento_reward_salesrule';

    const MAGENTO_REWARD_HISTORY = 'magento_reward_history';
    const MAGENTO_REWARD_HISTORY_CONVERT = 'rewardpoints_transaction';

    protected $columns;
    protected $columns_convert;

    protected $is_step;

    /**
     * start Convert
     */
    public function conVertData(){
        if($this->modelManager->isEnabled('Magento_Reward')){
            $this->convertStepsOne();
            $this->convertStepsTwo();
            $this->convertStepsThree();
            $this->convertStepsFour();
            $this->disableRewardFromMagento('Magento_Reward');
        }
    }

    /**
     * step One
     */
    public function convertStepsOne(){
        /** Convert magento_reward  */
        $this->is_step = self::MAGENTO_REWARD;
        $data = $this->getData(self::MAGENTO_REWARD);
        $this->columns = $this->getColumns(self::MAGENTO_REWARD);
        $this->columns_convert = $this->getColumns(self::MAGENTO_REWARD_CONVERT);
        $this->columns_convert[] = 'points_balance';
        /** convert points_balance to point_balance */
        $needChanges = array('point_balance'=>'points_balance');
        /** $default value */
        $default = array('is_notification'=> 1,
            'expire_notification'=>1);
        /** start convert */
        $this->startConvert($data,$needChanges,$default);
        /** update into database */
        $this->inserttable(self::MAGENTO_REWARD_CONVERT);
    }

    /**
     * step two
     */
    public function convertStepsTwo(){
        /** Convert magento_reward_rate */
        $this->is_step = self::MAGENTO_REWARD_RATE;
        $data = $this->getData(self::MAGENTO_REWARD_RATE);
        $this->columns = $this->getColumns(self::MAGENTO_REWARD_RATE);
        $this->columns_convert = $this->getColumns(self::MAGENTO_REWARD_RATE_CONVERT);
        $this->columns_convert[] = 'website_id';
        $this->columns_convert[] = 'customer_group_id';
        $this->columns_convert[] = 'currency_amount';

        /** convert website_id to website_ids,customer_group_id to customer_group_ids, currency_amount to money*/
        $needChanges = array('website_ids'=>'website_id',
                             'customer_group_ids'=>'customer_group_id',
                             'money'=>'currency_amount');
        /** @var  $default : default value */
        $default = array('direction'=> 2,
                         'status'=>1,
                         'sort_order'=>1,
                         'max_price_spended_type'=>'NULL',
                         'max_price_spended_value'=>'NULL');
        /** start convert && update into database */
        $this->startConvert($data,$needChanges,$default);
        $this->inserttable(self::MAGENTO_REWARD_RATE_CONVERT);
    }

    /**
     * step three
     */
    public function convertStepsThree(){
        /** Convert magento_reward_salesrule */
        /** not to do anything
         * in Magento : it's on base rule of cart price */
    }

    /**
     * step four
     */
    public function convertStepsFour(){
        /** Convert magento_reward_history */
        $this->is_step = self::MAGENTO_REWARD_HISTORY;
        $data = $this->getRewardHistory();
        $this->columns = $this->getColumns(self::MAGENTO_REWARD_HISTORY);
        $this->columns[] = 'customer_id';
        $this->columns[] = 'customer_email';
        $this->columns[] = 'message';

        $this->columns_convert = $this->getColumns(self::MAGENTO_REWARD_HISTORY_CONVERT);
        $this->columns_convert[] = 'customer_id';
        $this->columns_convert[] = 'customer_email';
        $this->columns_convert[] = 'message';
        $this->columns_convert[] = 'points_used';
        $this->columns_convert[] = 'points_delta';
        $this->columns_convert[] = 'created_at';

        /** convert message to title,points_used to point_used , points_delta to point_amount */
        $needChanges = array('title'=>'message',
                            'point_used'=>'points_used',
                            'point_amount'=>'points_delta',
                            'created_time'=>'created_at');
        /** @var  $default : default value */
        $default = array('status'=>3);
        /** start convert && update into database */
        $this->startConvert($data,$needChanges,$default);
        $this->inserttable(self::MAGENTO_REWARD_HISTORY_CONVERT);

    }

    /**
     * @param $item
     * @return mixed
     */
    public function dynamicItem($item){
        if($this->is_step == self::MAGENTO_REWARD_RATE){
            if($item['website_ids'] == 0){
                $item['website_ids'] = 1;
            }
            if($item['customer_group_ids'] == 0){
                $item['customer_group_ids']= '1,2,3';
            }
        }
        return $item;
    }
    /**
     * @param $moduleName
     */
    public function disableRewardFromMagento($moduleName){
        $outputPath = "advanced/modules_disable_output/$moduleName";
        $this->config->saveConfig($outputPath,true,'default',0);
    }

    /**
     * @return array
     */
    public function getRewardHistory(){
        $collection =  $this->rewardHistoryFactory->addCustomerInfo();
        $items = array();
        foreach ($collection as $item){
            $item->setMessage($this->escapeHtml($item->getMessage()));
            $items[] = $item->getData();
        }
        return $items;
    }
}