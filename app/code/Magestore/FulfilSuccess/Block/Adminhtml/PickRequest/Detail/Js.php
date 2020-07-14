<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\PickRequest\Detail;

use Magestore\FulfilSuccess\Api\Data\PickRequestInterface;

class Js extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Js constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->coreRegistry = $registry;
    }

    /**
     * @return bool
     */
    public function isPickedRequest(){
        $pickRequest = $this->coreRegistry->registry('current_pick_request');
        if($pickRequest && $pickRequest->getId()){
            return ($pickRequest->getData(PickRequestInterface::STATUS) == PickRequestInterface::STATUS_PICKED)?true:false;
        }
        return false;
    }
    
    /**
     * Get json string of config data
     * 
     * @return string
     */
    public function getConfigDataJson()
    {
        $config = [
            'pickRequestId' => $this->_request->getParam('pick_request_id'),
            'reloadViewDetailUrl' => $this->_urlBuilder->getUrl('*/*/getInfo'),
            'modalId' => 'pick_request_detail_holder',
            'recent_picked_listing' => 'os_fulfilsuccess_recent_picked_listing.recent_picked_listing_data_source',
            'pick_request_listing' => 'os_fulfilsuccess_pickrequest_listing.pickrequest_listing_data_source'
        ];
        if($this->isPickedRequest()){
            $config['printItemsButton'] = 'print_items';
            $config['printItemsUrl'] = $this->_urlBuilder->getUrl('*/*/printOrderItems');
        }else{
            $config['movetoNeedShipButton'] = 'move_to_need_ship';
            $config['movetoNeedShipUrl'] = $this->_urlBuilder->getUrl('*/*/moveNeedToShip');
            $config['markAskPickedButton'] = 'mark_picked';
            $config['markAskPickedUrl'] = $this->_urlBuilder->getUrl('*/*/markAsPicked');
            $config['markAskPickedAllButton'] = 'mark_picked_all';
            $config['markAskPickedAllUrl'] = $this->_urlBuilder->getUrl('*/*/markAsPickedAll');
        }
        return \Zend_Json::encode($config);
    }
}