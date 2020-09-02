<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Block\Adminhtml\Order\Button;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\ObjectManagerInterface;

abstract class ButtonAbstract implements ButtonProviderInterface
{
    
    const VERITY_STEP_ENABLE_CONFIG_PATH = 'ordersuccess/order/verify';
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var type @var EventManagerInterface
     */
    protected $eventManager;
    
    /**
     * @var RequestInterface 
     */
    protected $request;
    
    /**
     * @var ObjectManagerInterface 
     */
    protected $objectManager;
    
    
    public function __construct(
        EventManagerInterface $eventManager, 
        ScopeConfigInterface $scopeConfig,
        RequestInterface $request,
        ObjectManagerInterface $objectManager
    )
    {
        $this->eventManager = $eventManager;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->objectManager =  $objectManager;
    }
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = $this->prepareButtonData();
        $dataObject = new \Magento\Framework\DataObject($data);
        if($this->isCurrent()) {
            $dataObject->setData('disabled', true);
            $dataObject->setData('class', 'ordersuccess-current');
        } else {
            $dataObject->setData('class', 'ordersuccess'); 
        }
        
        $this->eventManager->dispatch(
                'ordersuccess_order_button_prepare_data',
                ['button_data' => $dataObject]
        );
        return $dataObject->toArray();
    }
    
    /**
     * is current this step
     * 
     * @return bool
     */
    abstract public function isCurrent();

    /**
     * @return array
     */
    abstract public function prepareButtonData();
    
}
