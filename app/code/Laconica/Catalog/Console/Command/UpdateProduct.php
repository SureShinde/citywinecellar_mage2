<?php

namespace Laconica\Catalog\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateProduct extends Command
{
    const ATTRIBUTE_ID_DESCRIPTION = 61;

    protected $connection;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        $name = null
    )
    {
        $this->connection = $resourceConnection->getConnection();
        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('la:catalog:update-product')
            ->setDescription('Update product');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('started');

        $select = $this->connection->select()->from('catalog_product_entity_text')
            ->where('attribute_id=?', self::ATTRIBUTE_ID_DESCRIPTION);

        $rows = $this->connection->fetchAll($select);
        if (!$rows) {
            return;
        }

        $dataToUpdate = [];
        foreach ($rows as $row) {
            preg_match('/[\x00-\x1F\x7F]/u', $row['value'], $matches);
            if (!$matches || !count($matches)) {
                continue;
            }

            $dataToUpdate[] = $row;
        }

        foreach ($dataToUpdate as $row) {
            $row['value'] = preg_replace('/[\x00-\x1F\x7F]/u', '', $row['value']);
            $this->connection->update(
                'catalog_product_entity_text',
                ['value' => $row['value']],
                '1=1'
                . ' and attribute_id=' . self::ATTRIBUTE_ID_DESCRIPTION
                . ' and store_id=' . $row['store_id']
                . ' and entity_id=' . $row['entity_id']
            );
            $output->write('.');
        }

        $output->writeln('finished');
    }

}
