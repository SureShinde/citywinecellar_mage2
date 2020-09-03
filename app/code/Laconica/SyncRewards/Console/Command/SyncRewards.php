<?php

namespace Laconica\SyncRewards\Console\Command;

use Amasty\Rewards\Api\Data\ExpirationArgumentsInterface;
use Amasty\Rewards\Api\RewardsProviderInterface;
use Amasty\Rewards\Api\Data\ExpirationArgumentsInterfaceFactory;
use Amasty\Rewards\Helper\Data;
use Magento\Framework\App\Filesystem\DirectoryList;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncRewards extends Command
{

    /**
     * @var \Magestore\Rewardpoints\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var RewardsProviderInterface
     */
    protected $rewardsProvider;

    /**
     * @var ExpirationArgumentsInterfaceFactory
     */
    protected $expirationArgFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        \Magestore\Rewardpoints\Model\ResourceModel\Customer\CollectionFactory $collectionFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        RewardsProviderInterface $rewardsProvider,
        ExpirationArgumentsInterfaceFactory $expirationArgFactory,
        \Psr\Log\LoggerInterface $logger,
        string $name = null
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->resource = $resource;
        $this->rewardsProvider = $rewardsProvider;
        $this->expirationArgFactory = $expirationArgFactory;
        $this->logger = $logger;
        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('laconica:sync_rewards')
            ->setDescription('Synchronize MagePos reward points with Amasty one.');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Start to transfer points</info>');
        $posCustomerCollection = $this->collectionFactory->create()->load();
        $output->writeln('<info>Count of customers: ' . $posCustomerCollection->getSize() . '</info>');
        $connection = $this->resource->getConnection();

        $transferred = 0;
        foreach ($posCustomerCollection as $item) {
            $customerId = $item->getCustomerId();
            $select = $connection->select()->from(
                $connection->getTableName('amasty_rewards_rewards'),
                [
                    'count' => 'count(*)'
                ]
            )->where(
                'customer_id = ?',
                (int)$customerId
            ) ->where('action like ?' , Data::TRANSFER_ACTION);

            $result = $connection->fetchRow($select);
            if (isset($result['count']) && $result['count'] == '0') {
                $expire = $this->expirationArgFactory->create();
                $expire->setIsExpire(false);
                $amount = $item->getPointBalance();
                if (intval($amount) > 0) {
                    try {
                        $this->rewardsProvider->addPoints($amount, $customerId, Data::TRANSFER_ACTION, '', $expire);
                        $transferred++;
                    } catch (\Exception $exception) {
                        $message = "Could not save info to customer with id = $customerId";
                        $output->writeln("<error>$message</error>");
                        $this->logger->error($message);
                        $this->logger->error($exception->getMessage());
                    }
                }
            }
        }
        $output->writeln('<info>Transfer has finished. Total count transfered customers\' points: ' . $transferred . '</info>');
    }
}
