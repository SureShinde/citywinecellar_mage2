<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Model\Locator;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Magento\Backend\Model\Session;
/**
 * Class RegistryLocator
 */
class Locator implements \Magestore\BarcodeSuccess\Model\Locator\LocatorInterface
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry,
        Session $session
    ) {
        $this->registry = $registry;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function add($key, $value){
        $this->session->setData($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key){
        return $this->session->getData($key);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key){
        $this->session->setData($key, null);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentBarcodeHistory()
    {
        $history = $this->session->getData('current_barcode_history');
        if($history){
            return $history;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentBarcodeHistory($history)
    {
        $this->session->setData('current_barcode_history',$history);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshSession()
    {
        $this->session->setData('current_barcode_history',null);
    }

}
