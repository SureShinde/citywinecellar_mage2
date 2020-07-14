<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Ui\Component;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magestore\FulfilSuccess\Api\PermissionManagementInterface;

/**
 * Class Batch
 */
class Batch extends \Magento\Ui\Component\AbstractComponent
{
    const NAME = 'batch';
    const REMOVE_BATCH_URL = 'fulfilsuccess/pickrequest/cancelbatch';

    /**
     * \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\Batch\CollectionFactory
     *
     */
    protected $batchCollectionFactory;

    /**
     * \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * \Magento\Framework\UrlInterface $urlInterface
     */
    protected $urlInterface;

    /**
     * \Magento\Framework\UrlInterface $request
     */
    protected $request;
    
    /**
     * @var PermissionManagementInterface
     */
    protected $permissionManagement;

    /**
     * Constructor
     *
     * @param \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\Batch\CollectionFactory $batchCollectionFactory
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Framework\App\RequestInterface $request
     * $param \Magento\Backend\Model\Auth\Session $authSession
     * @param ContextInterface $context
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        \Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\Batch\CollectionFactory $batchCollectionFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Backend\Model\Auth\Session $authSession,
        PermissionManagementInterface $permissionManagement,
        ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        $this->batchCollectionFactory = $batchCollectionFactory;
        $this->authSession = $authSession;
        $this->urlInterface = $urlInterface;
        $this->request = $request;
        $this->permissionManagement = $permissionManagement;
        $this->_data = $this->setDataOptions();
        parent::__construct($context, $components, $data);
    }

    /**
     * Get component name
     *
     * @return string
     */
    public function getComponentName()
    {
        return static::NAME;
    }

    /**
     * get remove batch url
     *
     * @return string
     */
    public function getRemoveBatchUrl($batchId = '')
    {
        $position = $this->request->getControllerName();
        $removeBatchUrl = $this->urlInterface->getUrl(self::REMOVE_BATCH_URL,
                                            [
                                                'batch_id' => $batchId,
                                                'position' => $position
                                            ]);
        return $removeBatchUrl;
    }

    /**
     * Set data options
     *
     * @return string
     */
    public function setDataOptions()
    {
        $batchs = $this->batchCollectionFactory->create();
        if(!$this->permissionManagement->checkPermission(PermissionManagementInterface::PICK_ALL_BATCH)) {
            $userId = $this->authSession->getUser()->getId();
            $batchs->addFieldToFilter('user_id', $userId);
        } else {
            $batchs->addUser();
        }
        
        $options = [
            'na' => [
                'value' => '0',
                'label' => __('No Batch'),
                'removeUrl' => null
            ]
        ];
        if($batchs->getSize()){
            foreach($batchs as $batch){
                $label = $batch->getUsername() ?  $batch->getCode() . ' ['. $batch->getUsername() .']' :  $batch->getCode();
                $options[$batch->getId()] = [
                                'value' => $batch->getId(),
                                'label' => $label,
                                'removeUrl' => $this->getRemoveBatchUrl($batch->getId())
                             ];
            }
        }
        $config = ['config' => [
                        'options' => $options
                  ]];
        return $config;
    }

    /**
     * Register component and apply paging settings to Data Provider
     *
     * @return void
     */
    public function prepare()
    {
        $this->prepareOptions();
        $paging = $this->getContext()->getRequestParam('batch_id');
        if (!isset($paging['notLimits'])) {
            $this->getContext()
                ->getDataProvider()
                ->setLimit($this->getOffset($paging), $this->getSize($paging));
        }

        parent::prepare();
    }

    /**
     * Prepare paging options
     *
     * @return void
     */
    public function prepareOptions()
    {
        $config = $this->getData('config');
        if (isset($config['options'])) {
            $config['options'] = array_values($config['options']);
            foreach ($config['options'] as &$item) {
                $item['value'] = (int) $item['value'];
            }
            unset($item);
            $this->setData('config', $config);
        }
    }


}
