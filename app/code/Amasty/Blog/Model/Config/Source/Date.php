<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Date
 */
class Date implements ArrayInterface
{
    /**
     * @var \Amasty\Blog\Helper\Date
     */
    private $date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    public function __construct(
        \Amasty\Blog\Helper\Date $date,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    ) {
        $this->date = $date;
        $this->dateTime = $dateTime;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $date = $this->dateTime->date('d M Y H:i:s', strtotime('-6 day'));

        return [
            [
                'value' => \Amasty\Blog\Helper\Date::DATE_TIME_PASSED,
                'label' => $this->date->getHumanizedDate($date)
            ],
            [
                'value' => \Amasty\Blog\Helper\Date::DATE_TIME_DIRECT,
                'label' => $this->date->renderDate($date, true)
            ],
        ];
    }
}
