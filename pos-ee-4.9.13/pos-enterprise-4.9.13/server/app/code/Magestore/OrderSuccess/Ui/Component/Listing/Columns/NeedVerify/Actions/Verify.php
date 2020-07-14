<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Ui\Component\Listing\Columns\NeedVerify\Actions;

use Magestore\OrderSuccess\Api\PermissionManagementInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Verify extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    
    /**
     * @var PermissionManagementInterface
     */
    protected $permissionManagement;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        PermissionManagementInterface $permissionManagement,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->permissionManagement = $permissionManagement;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    
    /**
     * Get component configuration
     *
     * @return array
     */
    public function getConfiguration()
    {
        $config = (array)$this->getData('config');
        if(!$this->permissionManagement->checkPermission(PermissionManagementInterface::VERIFY_ORDER)) {
            $config['visible'] = false;
        }
        return $config;
    }    

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$fieldName . '_html'] = "<button class='button'><span>".__('Process Verify')."</span></button>";
                $item[$fieldName . '_title'] = __('Process verifying Sales');
                $item[$fieldName . '_entity_id'] = $item['entity_id'];

                $url = $this->urlBuilder->getUrl('ordersuccess/needVerify/orderDetail', $params = array());
                $item[$fieldName . '_url'] = $url;
            }
        }

        return $dataSource;
    }
}