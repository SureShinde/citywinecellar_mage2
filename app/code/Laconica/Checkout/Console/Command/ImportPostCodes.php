<?php

namespace Laconica\Checkout\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Laconica\Checkout\Helper\StateConfig;

class ImportPostCodes extends Command
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    private $connection;
    /**
     * @var \Magento\Framework\App\Utility\Files $files
     */
    private $files;

    /**
     * @var \Magento\Framework\File\Csv $csv
     */
    private $csv;

    public function __construct(
        \Magento\Framework\App\Utility\Files $files,
        \Magento\Framework\File\Csv $csv,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        $name = null
    )
    {
        parent::__construct($name);
        $this->connection = $resourceConnection->getConnection();
        $this->files = $files;
        $this->csv = $csv;

    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('la:checkout:import-zip-codes')
            ->setDescription('Import zip codes');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Started:');
        try {
            $zipData = $this->readZipCsv();
        } catch (\Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());
            return;
        }
        $magentoRegionsId = $this->getMagentoRegionsId();
        $this->processDataImport($zipData, $magentoRegionsId, $output);
        $output->writeln('Finished:');
    }

    private function processDataImport(array $zipData, array $regions, OutputInterface $output)
    {
        $insertData = [];
        foreach ($zipData as $item) {
            $zipCode = $item['zipcode'] ?? null;
            $regionCode = $item['state'] ?? null;
            $regionId = $regions[$regionCode] ?? null;

            if (!$zipCode || !$regionCode || !$regionId) {
                continue;
            }
            $output->writeln("Added for insert: ZIP: {$zipCode}, Region: {$regionCode}, Magento ID: {$regionId}");
            array_push($insertData, [
                'zip_code' => $zipCode,
                'region_code' => $regionCode,
                'region_id' => $regionId
            ]);
        }
        if ($insertData) {
            $this->connection->truncateTable(StateConfig::ZIP_STATE_CONNECTION_TABLE);
            $this->connection->insertMultiple(StateConfig::ZIP_STATE_CONNECTION_TABLE, $insertData);
        }
        return $insertData;
    }

    private function getMagentoRegionsId()
    {
        $select = $this->connection->select()
            ->from('directory_country_region', ['code', 'region_id'])
            ->where('country_id LIKE ?', 'US');
        return $this->connection->fetchPairs($select);
    }

    /**
     * HEADERS:
     * [0] => zipcode
     * [1] => city
     * [2] => state
     * [3] => latitude
     * [4] => longitude
     * [5] => classification
     * [6] => population
     *
     * @return array
     * @throws \Exception
     */
    private function readZipCsv()
    {
        $file = $this->files->getModuleFile('Laconica', 'Checkout', 'data/zip-codes.csv');
        $data = $this->csv->getData($file);
        $headers = array_map('strtolower', array_shift($data));
        $result = [];
        foreach ($data as $item) {
            array_push($result, array_combine($headers, $item));
        }
        return $result;
    }
}