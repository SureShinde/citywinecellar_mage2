<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Ui\Component\Listing\PriceList;

use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;


class Columns extends \Magento\Ui\Component\Listing\Columns
{
    /**
     * @var \Magento\Framework\Authorization\PolicyInterface
     */
    protected $_policyInterface;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;
    
    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        \Magento\Framework\Authorization\PolicyInterface $policyInterface,
        \Magento\Backend\Model\Auth\Session $authSession,  
        array $components = [],
        array $data = []
    ) {
        $this->context = $context;
        $this->components = $components;
        $this->initObservers($data);
        $this->context->getProcessor()->register($this);
        $this->_data = array_replace_recursive($this->_data, $data);
        $this->_policyInterface = $policyInterface;
        $this->_authSession = $authSession;
    }    
    
    /**
     * Get component configuration
     *
     * @return array
     */
    public function getConfiguration()
    {
        $config = (array)$this->getData('config');
        if(!$this->isAllow()) {
            if(isset($config['editorConfig'])) {
                unset($config['editorConfig']);
            }
        }
        return $config;
    }    
    
    /**
     * 
     * @return bool
     */
    public function isAllow()
    {
        $user = $this->_authSession->getUser();
    
        return $this->_policyInterface->isAllowed($user->getRole()->getId(), 'Magestore_SupplierSuccess::supplier_pricinglist_edit');    
    }
    
}