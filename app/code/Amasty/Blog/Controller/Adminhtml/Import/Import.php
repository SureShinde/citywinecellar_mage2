<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Controller\Adminhtml\Import;

use Magento\Backend\App\Action\Context;
use Magento\Cron\Model\ScheduleFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Cron\Model\Schedule;

/**
 * Class Import
 */
class Import extends \Amasty\Blog\Controller\Adminhtml\Import
{
    const AMBLOG_CRON_DBHOST = 'amblog/cron/dbhost';
    const AMBLOG_CRON_DBUSERNAME = 'amblog/cron/dbusername';
    const AMBLOG_CRON_DBPASSWORD = 'amblog/cron/dbpassword';
    const AMBLOG_CRON_DBNAME = 'amblog/cron/dbname';
    const AMBLOG_CRON_PREFIX = 'amblog/cron/prefix';
    const AMBLOG_CRON_UPDATE = 'amblog/cron/update';

    /**
     * @var ScheduleFactory
     */
    private $scheduleFactory;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    private $configWritter;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\App\Config\ReinitableConfigInterface
     */
    private $reinitableConfig;

    public function __construct(
        Context $context,
        ScheduleFactory $scheduleFactory,
        DateTime $dateTime,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWritter,
        \Magento\Framework\App\Config\ReinitableConfigInterface $reinitableConfig,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scheduleFactory = $scheduleFactory;
        $this->dateTime = $dateTime;
        $this->configWritter = $configWritter;
        $this->scopeConfig = $scopeConfig;
        $this->reinitableConfig = $reinitableConfig;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $isValid = $this->validateAccess($data);
        if (!$isValid) {
            $this->_redirect('*/*/');
            return;
        }
        $this->saveDataInConfig($data);
        $this->createScheduleItem();

        $this->messageManager->addSuccessMessage(
            __('The import process was started successfully in accordance to the saved configuration')
        );
        $this->_redirect('*/*/');
    }

    /**
     * @param $data
     */
    private function saveDataInConfig($data)
    {
        $this->configWritter->delete(self::AMBLOG_CRON_DBHOST, $data['dbhost']);
        $this->configWritter->delete(self::AMBLOG_CRON_DBUSERNAME, $data['dbusername']);
        $this->configWritter->delete(self::AMBLOG_CRON_DBPASSWORD, $data['dbpassword']);
        $this->configWritter->delete(self::AMBLOG_CRON_DBNAME, $data['dbname']);
        $this->configWritter->delete(self::AMBLOG_CRON_PREFIX, $data['prefix']);
        $this->configWritter->delete(self::AMBLOG_CRON_UPDATE, $data['update']);
        $this->configWritter->save(self::AMBLOG_CRON_DBHOST, $data['dbhost']);
        $this->configWritter->save(self::AMBLOG_CRON_DBUSERNAME, $data['dbusername']);
        $this->configWritter->save(self::AMBLOG_CRON_DBPASSWORD, $data['dbpassword']);
        $this->configWritter->save(self::AMBLOG_CRON_DBNAME, $data['dbname']);
        $this->configWritter->save(self::AMBLOG_CRON_PREFIX, $data['prefix']);
        $this->configWritter->save(self::AMBLOG_CRON_UPDATE, $data['update']);
        $this->reinitableConfig->reinit();
    }

    /**
     * @param $data
     * @return bool
     */
    private function validateAccess($data)
    {
        try {
            // @codingStandardsIgnoreStart
            $connection = mysqli_connect($data['dbhost'], $data['dbusername'], $data['dbpassword'], $data['dbname']);
            $result = $connection->query('SELECT * FROM ' . $data['prefix'] . 'posts LIMIT 1');
            mysqli_close($connection);
            // @codingStandardsIgnoreEnd
            $isValid = true;
            if (!$result) {
                $this->messageManager->addErrorMessage(__('Invalid table prefix'));
                $isValid = false;
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('Invalid database credentials'));
            $isValid = false;
        }

        return $isValid;
    }

    private function createScheduleItem()
    {
        $currentDateTime = $this->dateTime->date();
        $scheduleDateTime = $this->dateTime->date(null, $this->dateTime->timestamp() + 20);
        $schedule = $this->scheduleFactory->create();
        $schedule->setMessages(null);
        $schedule->setJobCode('amblog_import');
        $schedule->setStatus(Schedule::STATUS_PENDING);
        $schedule->setCreatedAt($currentDateTime);
        $schedule->setScheduledAt($scheduleDateTime);
        $schedule->save();
    }
}
