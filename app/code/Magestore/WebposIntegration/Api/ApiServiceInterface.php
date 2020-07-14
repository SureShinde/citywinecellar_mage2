<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposIntegration\Api;

/**
 * @api
 */
interface ApiServiceInterface
{
    const API_INTEGRATION_NAME = "Magestore POS";

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param \Magento\Integration\Model\Integration $integration
     * @return string
     */
    public function getIntegrationAccessToken(\Magento\Integration\Model\Integration $integration);

    /**
     * @param \Magento\Integration\Model\Integration  $integration
     * @return $this|ApiServiceInterface
     * @throws \Exception
     */
    public function createAccessToken(\Magento\Integration\Model\Integration $integration);

    /**
     * @return $this
     */
    public function setupIntegration();

    /**
     * @return \Magento\Integration\Model\Integration
     * @throws \Exception
     */
    public function getIntegration();
}
