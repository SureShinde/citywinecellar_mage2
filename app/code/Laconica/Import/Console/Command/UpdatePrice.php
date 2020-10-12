<?php

namespace Laconica\Import\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdatePrice extends Command
{
    protected $attributeIdPrice = 64;
    protected $attributeIdSpecialPrice = 65;
    protected $attributeIdSpecialFromDate = 66;
    protected $attributeIdSpecialToDate = 67;

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
        $this->setName('la:import:update_price')
            ->setDescription('Update product prices from csv');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Started');
        $start = microtime(true);

        //$this->process('cwc');
        //$this->process('tls');
        $this->useDefaultValues();

        $end = gmdate("H:i:s", microtime(true) - $start);
        $output->writeln($end);
    }


    protected function process($website)
    {
        $items = $this->readCsvFile($website);
        echo $website . ':' . count($items) . "\n";

        $websiteId = $website == 'cwc' ? 1 : 2;

        foreach ($items as $item) {
            $sku = $item['HQID'] ?? false;
            $price = $item['Price'] ?? false;
            if (!$price || !$sku) {
                continue;
            }

            $productId = $this->getProductId($sku);
            if (!$productId) {
                continue;
            }

            $this->updateProductPrice($productId, $price, $websiteId);
            $this->removeSpecialPrice($productId, $websiteId);

            $saleType = $item['SaleType'] ?? false;
            if ($saleType != 1) {
                continue;
            }

            $salePrice = $item['SalePrice'];
            if (!$salePrice) {
                continue;
            }

            $startDate = $item['SaleStartDate'] ?? false;
            $endDate = $item['SaleEndDate'] ?? false;

            if (!$startDate || !$endDate) {
                continue;
            }

            if (date('Y-m-d') > $endDate) {
                continue;
            }

            $this->updateSpecialPrice($productId, $salePrice, $startDate, $endDate, $websiteId);
        }
    }

    protected function getProductId($sku)
    {
        $select = $this->connection->select()
            ->from('catalog_product_entity', 'entity_id')
            ->where('sku=?', $sku)
            ->limit(1);

        $id = $this->connection->fetchCol($select);
        $id = $id[0] ?? false;

        return $id;
    }

    protected function updateProductPrice($id, $price, $websiteId)
    {
        $id = intval($id);
        $price = (float)$price;

        $this->connection->insertOnDuplicate('catalog_product_entity_decimal', [
            'attribute_id' => $this->attributeIdPrice,
            'store_id'     => $websiteId,
            'entity_id'    => $id,
            'value'        => $price
        ],
            ['attribute_id', 'store_id', 'entity_id', 'value']
        );
    }

    protected function removeSpecialPrice($id, $websiteId)
    {
        $this->connection->update('catalog_product_entity_decimal', [
            'value' => null
        ], 'entity_id=' . $id . ' and attribute_id=' . $this->attributeIdSpecialPrice . ' and store_id=' . $websiteId);
    }

    protected function updateSpecialPrice($id, $salePrice, $startDate, $endDate, $websiteId)
    {
        $this->connection->insertOnDuplicate('catalog_product_entity_decimal', [
            'attribute_id' => $this->attributeIdSpecialPrice,
            'store_id'     => $websiteId,
            'entity_id'    => $id,
            'value'        => $salePrice
        ],
            ['attribute_id', 'store_id', 'entity_id', 'value']
        );

        $this->connection->insertOnDuplicate('catalog_product_entity_datetime', [
            'attribute_id' => $this->attributeIdSpecialFromDate,
            'store_id'     => $websiteId,
            'entity_id'    => $id,
            'value'        => $startDate
        ],
            ['attribute_id', 'store_id', 'entity_id', 'value']
        );

        $this->connection->insertOnDuplicate('catalog_product_entity_datetime', [
            'attribute_id' => $this->attributeIdSpecialToDate,
            'store_id'     => $websiteId,
            'entity_id'    => $id,
            'value'        => $endDate
        ],
            ['attribute_id', 'store_id', 'entity_id', 'value']
        );

        echo '.';
    }

    protected function useDefaultValues()
    {
        $prices = $this->getEqualAttributes($this->attributeIdPrice);
        $entityIds = array_column($prices, 'entity_id');

        foreach ($prices as $row) {
            $this->connection->update(
                'catalog_product_entity_decimal',
                ['value' => $row['value']],
                'entity_id=' . $row['entity_id'] . ' and store_id=0 and attribute_id=' . $this->attributeIdPrice
            );

            echo '.';
        }

        $this->connection->delete('catalog_product_entity_decimal', [
            'attribute_id=?'   => $this->attributeIdPrice,
            'store_id > ?' => '0',
            'entity_id in (?)' => $entityIds
        ]);

        //$specialPrices = $this->getEqualAttributes($this->attributeIdSpecialPrice);
    }

    protected function getEqualAttributes($attributeId)
    {
        $select = $this->connection->select()
            ->from(['cpe' => 'catalog_product_entity'], 'entity_id')
            ->joinInner(
                ['price_1' => 'catalog_product_entity_decimal'],
                'price_1.entity_id=cpe.entity_id and price_1.attribute_id=' . $attributeId . ' and price_1.store_id=1',
                []
            )
            ->joinInner(
                ['price_2' => 'catalog_product_entity_decimal'],
                'price_2.entity_id=cpe.entity_id and price_2.attribute_id=' . $attributeId . ' and price_2.store_id=2',
                []
            )
            ->columns(['value' => 'price_1.value'])
            ->where('price_1.value=price_2.value');

        $rows = $this->connection->fetchAll($select);

        return $rows;
    }

    private function readCsvFile(string $website): array
    {
        $this->csvReader->setDelimiter(',');
        $this->csvReader->setEnclosure('"');
        $fullPath = $this->files->getModuleFile('Laconica', 'Import', 'data/price_' . $website . '.csv');
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