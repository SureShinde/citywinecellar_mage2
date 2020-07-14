<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\WebposStripeTerminal\Controller\Adminhtml\Api;

/**
 * Class Test
 * @package Magestore\WebposStripeTerminal\Controller\Adminhtml\Api
 */
class Test extends \Magestore\WebposStripeTerminal\Controller\Adminhtml\AbstractAction
{
    /**
     * @return \Magento\Framework\Controller\Result\Json $resultJson
     */
    public function execute()
    {
        $response = [
            'message' => '',
            'success' => true,
        ];
        try {
            $this->service->validateEnv();
            $this->service->connectToApi();
        } catch (\Exception $exception) {
            $response['success'] = false;
            $response['message'] = __($exception->getMessage());
        }

        return $this->createJsonResult($response);
    }
}
