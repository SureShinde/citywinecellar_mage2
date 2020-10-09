<?php

namespace Laconica\Import\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdatePrice extends Command
{
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
        echo "execute\n";
    }
}