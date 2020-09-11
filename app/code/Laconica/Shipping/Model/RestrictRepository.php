<?php

namespace Laconica\Shipping\Model;

class RestrictRepository
{
    protected $connection;
    protected $logger;
    protected $config;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Psr\Log\LoggerInterface $logger,
        \Laconica\Shipping\Helper\Config $config
    )
    {
        $this->connection = $resourceConnection->getConnection();
        $this->logger = $logger;
        $this->config = $config;
    }

    public function hasRestrictions($carrierCode, $request)
    {
        //$start = microtime(true);

        $select = $this->connection->select()->from(['main_table' => 'la_table_rate_excluded'], ['count' => 'count(main_table.id)']);
        $this->addZipFromFilter($select, $request->getDestPostcode());
        $this->addZipToFilter($select, $request->getDestPostcode());
        $this->addZipToFilter($select, $request->getDestPostcode());
        $this->addStateFilter($select, $request->getDestRegionId());
        $this->addCityFilter($select, $request->getDestRegionId());
        $select->where("store_id={$request->getStoreId()}");

        $hasRestrictions = $this->connection->fetchCol($select);
        $hasRestrictions = $hasRestrictions[0] ?? false;
        //$hasRestrictions = $this->getCarrierRestrictions($carrierCode, $request, $hasRestrictions);

        //$time = microtime(true) - $start;
        //$this->logger->critical($carrierCode . ' : ' . $time);

        return $hasRestrictions;
    }

    public function getCarrierRestrictions($carrierCode, $request, $hasRestrictions)
    {
        if ($hasRestrictions || $carrierCode != 'fedex') {
            return $hasRestrictions;
        }

        $select = $this->connection->select()->from(['main_table' => 'amasty_table_rate'], ['count' => 'count(main_table.id)']);
        $this->addZipFromFilter($select, $request->getDestPostcode());
        $this->addZipToFilter($select, $request->getDestPostcode());
        $this->addZipToFilter($select, $request->getDestPostcode());
        $this->addStateFilter($select, $request->getDestRegionId());
        $this->addCityFilter($select, $request->getDestRegionId());
        $this->addStoresFilter($select, $request->getStoreId());
        $this->addLocalDeliveryFilter($select);

        //$this->logger->critical($carrierCode . ' : ' . (string)$select);

        $hasRestrictions = $this->connection->fetchCol($select);
        $hasRestrictions = $hasRestrictions[0] ?? false;

        return $hasRestrictions;
    }

    protected function addZipFromFilter($select, $postcode)
    {
        $select->where("(`num_zip_from` <= {$postcode} OR `zip_from` = '')");
    }

    protected function addZipToFilter($select, $postcode)
    {
        $select->where("(`num_zip_to` >= {$postcode} OR `zip_to` = '')");
    }

    protected function addCountryFilter($select, $countryId)
    {
        $select->where("(((`country` = '{$countryId}') or (`country` = '0') or (`country` = '')))");
    }

    protected function addStateFilter($select, $state)
    {
        $select->where("(((`state` = {$state}) or (`state` = '0') or (`state` = '')))");
    }

    protected function addCityFilter($select, $city)
    {
        $select->where("(((`city` like '{$city}') or (`city` = '0') or (`city` = '')))");
    }

    protected function addStoresFilter($select, $storeId)
    {
        $select->joinInner(
            ['method' => 'amasty_table_method'],
            'main_table.method_id = method.id',
            []
        );

        $select->where('stores="" OR FIND_IN_SET("' . $storeId . '", `stores`)');
    }

    protected function addLocalDeliveryFilter($select)
    {
        $names = $this->config->getRestrictedRatesNames();
        if (!$names) {
            return;
        }

        foreach ($names as $name) {
            if (!$name) {
                continue;
            }

            $select->where('name_delivery like "%' . $name . '%"');
        }
    }
}