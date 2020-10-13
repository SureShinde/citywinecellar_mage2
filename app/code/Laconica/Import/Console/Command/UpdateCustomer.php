<?php

namespace Laconica\Import\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCustomer extends Command
{
    protected $attributeIds;

    /**
     * @var \Magento\Framework\File\Csv $csvReader
     */
    private $csvReader;

    /**
     * @var \Magento\Framework\App\Utility\Files $files
     */
    private $files;

    /**
     * @var \Psr\Log\LoggerInterface $logger
     */
    private $logger;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    private $connection;

    /**
     * UpdateCustomer constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\File\Csv $csvReader
     * @param \Magento\Framework\App\Utility\Files $files
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param null $name
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\File\Csv $csvReader,
        \Magento\Framework\App\Utility\Files $files,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        $name = null
    )
    {
        parent::__construct($name);

        $this->logger = $logger;
        $this->csvReader = $csvReader;
        $this->files = $files;
        $this->connection = $resourceConnection->getConnection();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('la:import:update_customer')
            ->setDescription('Update customer from csv');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Started');
        $start = microtime(true);

        $this->process('cwc');
        $this->process('tls');

        $end = gmdate("H:i:s", microtime(true) - $start);
        $output->writeln($end);
    }


    protected function process($website)
    {
        $items = $this->readCsvFile($website);
        echo $website . ':' . count($items) . "\n";
        $websiteId = $website == 'cwc' ? 1 : 2;

        foreach ($items as $item) {
            $item = array_map('trim', $item);
            $email = $item['EmailAddress'] ?? false;
            $accountNumber = $item['countNumber'] ?? false;
            if (!$email || !$accountNumber) {
                continue;
            }

            $this->updateAttribute($email, $accountNumber, 'account_number', $websiteId);
        }
    }

    protected function updateAttribute($email, $value, $attributeCode, $websiteId)
    {
        $customerId = $this->getCustomerId($email, $websiteId);
        if (!$customerId) {
            return;
        }

        $this->connection->insertOnDuplicate('customer_entity_varchar', [
            'attribute_id' => $this->getAttributeId($attributeCode),
            'entity_id'    => $customerId,
            'value'        => $value
        ],
            ['attribute_id', 'entity_id', 'value']
        );

        echo '.';
    }

    protected function getCustomerId($email, $websiteId)
    {
        $select = $this->connection->select()
            ->from('customer_entity', 'entity_id')
            ->where('email=?', $email)
            ->where('website_id=?', $websiteId)
            ->limit(1);

        $id = $this->connection->fetchCol($select);
        $id = $id[0] ?? false;

        return $id;
    }

    protected function getAttributeId($attributeCode)
    {
        if (!isset($this->attributeIds[$attributeCode])) {

            $select = $this->connection->select()
                ->from('eav_attribute', 'attribute_id')
                ->where('attribute_code=?', $attributeCode)
                ->limit(1);

            $id = $this->connection->fetchCol($select);
            $this->attributeIds[$attributeCode] = $id[0] ?? false;
        }

        return $this->attributeIds[$attributeCode];
    }

    private function readCsvFile(string $website): array
    {
        $this->csvReader->setDelimiter(',');
        $this->csvReader->setEnclosure('"');
        $fullPath = $this->files->getModuleFile('Laconica', 'Import', 'data/customer_' . $website . '.csv');
        try {
            $csvItems = $this->csvReader->getData($fullPath);
        } catch (\Exception $e) {
            return [];
        }
        $headers = array_shift($csvItems);
        $items = [];
        foreach ($csvItems as $item) {
            array_push($items, array_combine($headers, $item));
        }

        return $items;
    }
}
