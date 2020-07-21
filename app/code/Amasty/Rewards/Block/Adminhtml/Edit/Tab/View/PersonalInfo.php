<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Block\Adminhtml\Edit\Tab\View;

use Amasty\Rewards\Api\Data\ExpirationDateInterface;
use Amasty\Rewards\Model\ConstantRegistryInterface;
use Amasty\Rewards\Model\Date;
use Amasty\Rewards\Model\ResourceModel\Expiration;
use Magento\Backend\Block\Template;
use Magento\Framework\Registry;
use Magento\Customer\Controller\RegistryConstants;

class PersonalInfo extends Template
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Date
     */
    private $date;

    /**
     * @var Expiration
     */
    private $expiration;

    public function __construct(
        Template\Context $context,
        Expiration $expiration,
        Registry $registry,
        Date $date,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->expiration = $expiration;
        $this->registry = $registry;
        $this->date = $date;
    }

    public function getStatistic()
    {
        return $this->registry->registry(ConstantRegistryInterface::CUSTOMER_STATISTICS);
    }

    /**
     * @return \Magento\Framework\Phrase|null
     */
    public function getDeadlineComment()
    {
        $expirationRow =
            $this->expiration->getClosest($this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID));

        if (!$date = $expirationRow[ExpirationDateInterface::DATE]) {
            return null;
        }

        $storeCode = $this->_storeManager->getStore()->getCode();

        return __(
            '<b>%1</b> points will be deducted from the balance <b>%2</b> because of expiration.',
            $expirationRow[ExpirationDateInterface::AMOUNT],
            $this->date->convertDate($date, $storeCode, \IntlDateFormatter::FULL)
        );
    }
}
