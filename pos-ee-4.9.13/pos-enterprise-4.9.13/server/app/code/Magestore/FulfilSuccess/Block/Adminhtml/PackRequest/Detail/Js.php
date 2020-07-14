<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Block\Adminhtml\PackRequest\Detail;

use Magestore\FulfilSuccess\Api\Data\PackRequestInterface;

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
    public function isPackedRequest()
    {
        $packRequest = $this->coreRegistry->registry('current_pack_request');
        if ($packRequest && $packRequest->getId()) {
            if (in_array($packRequest->getData(PackRequestInterface::STATUS),
                [PackRequestInterface::STATUS_PACKED, PackRequestInterface::STATUS_CANCELED])) {
                return true;
            }
//            return ($packRequest->getData(PackRequestInterface::STATUS) == PackRequestInterface::STATUS_PACKED) ? true : false;
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
            'packRequestId' => $this->_request->getParam('pack_request_id'),
            'reloadViewDetailUrl' => $this->_urlBuilder->getUrl('*/*/getInfo'),
            'modalId' => 'pack_request_detail_holder',
            'recent_packed_listing' => 'os_fulfilsuccess_recent_packed_listing.recent_packed_listing_data_source',
            'pack_request_listing' => 'os_fulfilsuccess_packrequest_listing.packrequest_listing_data_source'
        ];
        if ($this->isPackedRequest()) {
            $config['printItemsButton'] = 'print_items';
            $config['printItemsUrl'] = $this->_urlBuilder->getUrl('*/*/printOrderItems');
        } else {
            $config['movetoPickButton'] = 'move_to_pick';
            $config['movetoPickUrl'] = $this->_urlBuilder->getUrl('*/*/moveToPick');
            $config['markAskPackedAllButton'] = 'pack_all';
            $config['markAskPackedAllUrl'] = $this->_urlBuilder->getUrl('*/*/markAsPackedAll');
        }
        return \Zend_Json::encode($config);
    }
}