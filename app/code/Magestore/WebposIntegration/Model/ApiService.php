<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposIntegration\Model;

use Magento\Integration\Model\Integration;
use Magestore\WebposIntegration\Api\ApiServiceInterface;

/**
 * Class ApiService
 * @package Magestore\WebposIntegration\Model
 */
class ApiService implements \Magestore\WebposIntegration\Api\ApiServiceInterface
{

    /**
     * @var \Magento\Integration\Api\IntegrationServiceInterface
     */
    protected $integrationService;

    /**
     * @var \Magento\Integration\Api\OauthServiceInterface
     */
    protected $oauthService;

    /**
     * @var \Magento\Integration\Model\ConfigBasedIntegrationManager
     */
    protected $integrationManager;

    /**
     * ApiService constructor.
     * @param \Magento\Integration\Api\IntegrationServiceInterface $integrationService
     * @param \Magento\Integration\Api\OauthServiceInterface $oauthService
     * @param \Magento\Integration\Model\ConfigBasedIntegrationManager $integrationManager
     */
    public function __construct(
        \Magento\Integration\Api\IntegrationServiceInterface $integrationService,
        \Magento\Integration\Api\OauthServiceInterface $oauthService,
        \Magento\Integration\Model\ConfigBasedIntegrationManager $integrationManager
    ) {
        $this->integrationService = $integrationService;
        $this->oauthService = $oauthService;
        $this->integrationManager = $integrationManager;
    }

    /**
     * @return string
     */
    public function getToken(){
        try{
            $integration = $this->getIntegration();
        }catch (\Exception $e){
            $this->setupIntegration();
            $integration = $this->getIntegration();
        }
        $token = $this->getIntegrationAccessToken($integration);
        if(!$token){
            $integration = $this->createAccessToken($integration);
            $token = $this->getIntegrationAccessToken($integration);
        }
        return $token;
    }

    /**
     * @param Integration $integration
     * @return string
     */
    public function getIntegrationAccessToken(\Magento\Integration\Model\Integration $integration){
        $token = '';
        if(($integration->getStatus() == Integration::STATUS_ACTIVE) && $integration->getConsumerId()){
            $accessToken = $this->oauthService->getAccessToken($integration->getConsumerId());
            if($accessToken && $accessToken->getToken()){
                $token = $accessToken->getToken();
            }
        }
        return $token;
    }

    /**
     * @param \Magento\Integration\Model\Integration $integration
     * @return \Magento\Integration\Model\Integration
     * @throws \Exception
     */
    public function createAccessToken(\Magento\Integration\Model\Integration $integration){
        if ($this->oauthService->createAccessToken($integration->getConsumerId(), true)) {
            $integration->setStatus(Integration::STATUS_ACTIVE)->save();
        }
        return $integration;
    }

    /**
     * @return $this
     */
    public function setupIntegration(){
        $this->integrationManager->processIntegrationConfig([ApiServiceInterface::API_INTEGRATION_NAME]);
        return $this;
    }

    /**
     * @return \Magento\Integration\Model\Integration
     * @throws \Exception
     */
    public function getIntegration(){
        $integration = $this->integrationService->findByName(self::API_INTEGRATION_NAME);
        if($integration && $integration->getIntegrationId()){
            return $integration;
        }
        throw new \Exception(__('POS Integration has not been setup correctly yet'));
    }
}
