<?php

namespace Laconica\SyncRewards\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Amasty\Rewards\Helper\Data;

class SyncRewardsExpireDate extends Command
{

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    private $connection;


    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        string $name = null
    )
    {
        parent::__construct($name);
        $this->connection = $resourceConnection->getConnection();
    }


    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('laconica:sync_rewards_expire_date')
            ->setDescription('Synchronize Amasty expire date.');

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
        $this->syncExpirationDate();
        $output->writeln('');
        $output->writeln('<info>Finished point transfer</info>');
    }

    private function syncExpirationDate()
    {
        // Select customer deduct points amount
        $selectNotExpired = $this->connection->select()
            ->from(
                $this->connection->getTableName('amasty_rewards_rewards'),
                ['customer_id', 'amount' => 'sum(amount)']
            )
            ->where('expiration_id = ?', 0)
            ->group('customer_id');
        $notExpired = $this->connection->fetchPairs($selectNotExpired);

        // Select customer total amount
        $select = $this->connection->select()
            ->from(
                $this->connection->getTableName('amasty_rewards_rewards'),
                ['amount' => 'sum(amount)', 'customer_id', 'expiration_id']
            )
            ->group('customer_id')
            ->group('expiration_id');
        $selectResult = $this->connection->fetchAll($select);


        foreach ($selectResult as $key => $item) {

            // Check should we deduct points for customer
            if (isset($notExpired[$item['customer_id']]) && $item['amount'] >= abs($notExpired[$item['customer_id']])) {
                $expireAmount = $item['amount'] + $notExpired[$item['customer_id']];
                unset($notExpired[$item['customer_id']]);
            }else{
                $expireAmount = $item['amount'];
            }

            $expireAmount = ($expireAmount < 0) ? 0 : $expireAmount;

            $this->connection->update($this->connection->getTableName('amasty_rewards_expiration_date'),
                ['amount' => $expireAmount],
                [
                    'entity_id = ?' => $item['expiration_id'],
                    'customer_id = ?' => $item['customer_id']
                ]
            );
            if ($key % 100 === 0) {
                echo ".";
            }
        }
    }

}
