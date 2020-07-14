<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Plugin\Integration;

/**
 * Class Data
 * @package Magestore\Webpos\Plugin\Integration
 */
class Data
{
    /**
     *
     */
    const PATH = 'integration';

    /**
     * @var \Magento\Framework\Webapi\Rest\Request
     */
    protected $request;

    /**
     * Validator constructor.
     * @param \Magento\Framework\Webapi\Rest\Request $request
     */
    public function __construct(
        \Magento\Framework\Webapi\Rest\Request $request
    ){
        $this->request = $request;
    }

    public function beforeMapResources(\Magento\Integration\Helper\Data $helper, array $resources)
    {
        $restricted = $this->getRestrictedIds();
        foreach ($resources as $key => $resource) {
            if (in_array($resource['id'], $restricted)) {
                unset($resources[$key]);
            }
        }
        return [$resources];
    }

    public function getRestrictedIds()
    {
        $pathInfo = $this->request->getPathInfo();
        if (strpos($pathInfo, self::PATH) === false) {
            return ['Magestore_Webpos::posclient'];
        }

        return [];

    }
}