<?php

namespace Laconica\Checkout\Controller\Connection;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Laconica\Checkout\Helper\StateConfig;

class Zip implements HttpPostActionInterface
{
    private $jsonResultFactory;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    private $connection;

    /**
     * @var \Magento\Framework\App\RequestInterface $request
     */
    private $request;

    /**
     * @var StateConfig $stateConfigHelper
     */
    private $stateConfigHelper;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\ResourceConnection $resourceConnection, // Temporary Solution
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        StateConfig $stateConfigHelper
    )
    {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->connection = $resourceConnection->getConnection();
        $this->request = $request;
        $this->stateConfigHelper = $stateConfigHelper;
    }

    public function execute()
    {
        $zipCode = $this->request->getParam('zip', null);
        $regionId = $this->request->getParam('region', null);

        $data = [
            'status' => false,
            'region_common' => false
        ];

        if ($zipCode && $regionId) {
            $validStates = $this->stateConfigHelper->getCommonAllowedStates();
            $zipStateConnection = $this->checkZipStateConnection($zipCode, $regionId);
            $data = [
                'status' => isset($zipStateConnection['zip_code']),
                'region_common' => in_array($regionId, $validStates)
            ];
        }

        $result = $this->jsonResultFactory->create();
        $result->setData($data);
        return $result;
    }


    private function checkZipStateConnection($zipCode, $regionId)
    {
        $select = $this->connection->select()
            ->from(StateConfig::ZIP_STATE_CONNECTION_TABLE, ['zip_code'])
            ->where('zip_code = ?', $zipCode)
            ->where('region_id = ?', $regionId);
        return $this->connection->fetchRow($select);
    }
}