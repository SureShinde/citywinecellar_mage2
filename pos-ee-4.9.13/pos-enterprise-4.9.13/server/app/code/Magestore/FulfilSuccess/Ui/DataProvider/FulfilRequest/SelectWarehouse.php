<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Ui\DataProvider\FulfilRequest;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

abstract class SelectWarehouse extends \Magento\Ui\Component\Form\Element\Select
{
    /**
     * @var \Magestore\FulfilSuccess\Service\Location\LocationServiceInterface 
     */
    protected $locationService;
    
    
    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param array|OptionSourceInterface|null $options
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        \Magestore\FulfilSuccess\Service\Location\LocationServiceInterface $locationService,
        $options = null,
        array $components = [],
        array $data = []
    ) {
        $this->locationService = $locationService;
        parent::__construct($context, $options, $components, $data);
    }
    
    /**
     * Get action url path
     * 
     * @return string
     */
    abstract public function getUrlPath();
    
    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        $this->options = $this->options ? $this->options : $this->getData('options');
        $config = $this->getData('config');
        $config['default'] = $this->locationService->getCurrentWarehouseId();    
        $config['actionUrl'] = $this->context->getUrl($this->getUrlPath());
        /** @var \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement */
        $fulfilManagement = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magestore\FulfilSuccess\Api\FulfilManagementInterface');
        $config['isMSIEnable'] = $fulfilManagement->isMSIEnable();
        $this->setData('config', (array)$config);
       
        parent::prepare();
    }

}
