<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposIntegration\Plugin\Controller;

/**
 * Class Rest
 * @package Magestore\WebposIntegration\Plugin\Controller
 */
class Rest
{
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var \Magestore\WebposIntegration\Controller\Rest\RequestProcessor
     */
    protected $requestProcessor;

    /**
     * @var \Magento\Framework\Webapi\Rest\Request
     */
    protected $restRequest;

    /**
     * @var \Magento\Webapi\Controller\PathProcessor
     */
    protected $pathProcessor;
    
    /**
     * @var \Magento\Framework\Webapi\Rest\Response
     */
    protected $response;
    
    /**
     * @var \Magento\Framework\Webapi\ErrorProcessor
     */
    protected $errorProcessor;

    /**
     * @var \Magento\Framework\App\AreaList
     */
    protected $areaList;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * Rest constructor.
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magestore\WebposIntegration\Controller\Rest\RequestProcessor $requestProcessor
     * @param \Magento\Framework\Webapi\Rest\Request $restRequest
     * @param \Magento\Webapi\Controller\PathProcessor $pathProcessor
     * @param \Magento\Framework\Webapi\Rest\Response $response
     * @param \Magento\Framework\Webapi\ErrorProcessor $errorProcessor
     * @param \Magento\Framework\App\AreaList $areaList
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magestore\WebposIntegration\Controller\Rest\RequestProcessor $requestProcessor,
        \Magento\Framework\Webapi\Rest\Request  $restRequest,
        \Magento\Webapi\Controller\PathProcessor $pathProcessor,
        \Magento\Framework\Webapi\Rest\Response $response,
        \Magento\Framework\Webapi\ErrorProcessor $errorProcessor,
        \Magento\Framework\App\AreaList $areaList,
        \Magento\Framework\App\State $appState
    )
    {
        $this->productMetadata = $productMetadata;
        $this->requestProcessor = $requestProcessor;
        $this->restRequest = $restRequest;
        $this->pathProcessor = $pathProcessor;
        $this->response = $response;
        $this->errorProcessor = $errorProcessor;
        $this->areaList = $areaList;
        $this->appState = $appState;
    }

    /**
     * @param \Magento\Webapi\Controller\Rest $subject
     * @param callable $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     * @return mixed
     */
    public function aroundDispatch(\Magento\Webapi\Controller\Rest $subject, callable $proceed, \Magento\Framework\App\RequestInterface $request)
    {
        if(version_compare($this->productMetadata->getVersion(), '2.2.6', '<')){
            $path = $this->pathProcessor->process($request->getPathInfo());
            $this->restRequest->setPathInfo($path);
            if($this->requestProcessor->canProcess($this->restRequest)){
                $this->areaList->getArea($this->appState->getAreaCode())
                    ->load(\Magento\Framework\App\Area::PART_TRANSLATE);
                try {
                    $this->response = $this->requestProcessor->process($this->restRequest);
                } catch (\Exception $e) {
                    $maskedException = $this->errorProcessor->maskException($e);
                    $this->response->setException($maskedException);
                }
                return $this->response;
            }
        }
        return $proceed($request);
    }
}
