<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposZippay\Controller\Adminhtml\Api;

/**
 * Class Test
 * @package Magestore\WebposZippay\Controller\Adminhtml\Api
 */
class Test extends \Magestore\WebposZippay\Controller\Adminhtml\AbstractAction
{
    /**
     * @return \Magento\Framework\Controller\Result\Json $resultJson
     */
    public function execute()
    {

        $isEnable = $this->zippayService->isEnable();
        $params = $this->getRequest()->getParams();

        if (empty($params['api_url']) || empty($params['api_key'])) {
            return $this->createJsonResult(array(
                'url' => '',
                'message' => __('Zippay application api url and api key are required'),
                'success' => false
            ));
        }

        if (!$isEnable) {
            return $this->createJsonResult(array(
                'url' => '',
                'message' => $this->zippayService->getConfigurationError(),
                'success' => false
            ));
        }

        $response = [
            'url' => '',
            'message' => '',
            'success' => true
        ];

        // case 1: saved config + re test api
        // case 2: not save config + click test api
        if ($params['api_key'] === '******') {
            $connected = $this->zippayService->canConnectToApi();
        } else {
            $connected = $this->zippayService->canConnectToApi($params['api_url'], $params['api_key']);
        }

        $response['success'] = ($connected) ? true : false;
        $response['message'] = ($connected) ? '' : __('Connection failed. Please contact admin to check the configuration of API.');
        return $this->createJsonResult($response);
    }
}
