<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */


namespace Amasty\Blog\Helper;

/**
 * Class
 */
class Date extends \Magento\Framework\App\Helper\AbstractHelper
{
    const DATE_TIME_PASSED = 'passed';

    const DATE_TIME_DIRECT = 'direct';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $timezoneInterface;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    private $resolverInterface;

    /**
     * @var Settings
     */
    private $helperSettings;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Locale\ResolverInterface $resolverInterface,
        \Amasty\Blog\Helper\Settings $helperSettings,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        parent::__construct($context);
        $this->timezoneInterface = $timezoneInterface;
        $this->resolverInterface = $resolverInterface;
        $this->helperSettings = $helperSettings;
        $this->date = $date;
    }

    /**
     * @param $datetime
     * @return string
     */
    public function renderTime($datetime)
    {
        $date = $this->timezoneInterface->formatDateTime(
            $datetime,
            \IntlDateFormatter::NONE,
            \IntlDateFormatter::SHORT,
            $this->resolverInterface->getLocale()
        );

        return $date;
    }

    /**
     * @param $date
     * @return bool
     */
    private function isToday($date)
    {
        $today = $nowDate = $this->date->gmtDate('Ymd');
        $day = $this->timezoneInterface->convertConfigTimeToUtc($date, 'Ymd');

        return $today == $day;
    }

    /**
     * @param $date
     * @return bool
     */
    private function isYesterday($date)
    {
        $today = $nowDate = $this->date->gmtDate('Ymd');
        $day = $this->timezoneInterface->convertConfigTimeToUtc($date, 'Ymd');

        return ($today - 1) == $day;
    }

    /**
     * @param $date
     * @return \Magento\Framework\Phrase
     */
    public function getHumanizedDate($date)
    {
        $nowDate = $this->date->gmtDate();
        $timestamp = $this->date->gmtTimestamp($nowDate) - $this->date->gmtTimestamp($date);

        if ($this->isToday($date) || ($timestamp <= 0)) {
            return __("Today");
        } elseif ($this->isYesterday($date)) {
            return __("Yesterday");
        } else {
            # Nice correction
            $days = round($timestamp / (3600 * 24));
            $months = round($timestamp / (3600 * 24 * 30));
            $years = round($timestamp / (3600 * 24 * 30 * 12));

            if ($days < 30) {
                if ($days == 1) {
                    return __("%1 days ago", $days);
                } else {
                    return __("%1 day ago", $days);
                }
            } elseif ($months < 12) {
                if ($months == 1) {
                    return __("%1 month ago", $months);
                } else {
                    return __("%1 months ago", $months);
                }
            } else {
                if ($years == 1) {
                    return __("%1 year ago", $years);
                } else {
                    return __("%1 years ago", $years);
                }
            }
        }
    }

    /**
     * @param $datetime
     * @param bool $forceDirect
     * @param bool $dateFormat
     * @return bool|\Magento\Framework\Phrase|string
     */
    public function renderDate($datetime, $forceDirect = false, $dateFormat = false)
    {
        $date = $this->timezoneInterface->formatDateTime(
            $datetime,
            \IntlDateFormatter::LONG,
            \IntlDateFormatter::NONE,
            $this->resolverInterface->getLocale()
        );

        if (!$dateFormat) {
            $dateFormat = $this->helperSettings->getDateFormat();
        }

        if ($forceDirect || ($dateFormat == self::DATE_TIME_DIRECT)) {
            return $date;
        } else {
            return $this->getHumanizedDate($datetime);
        }
    }
}
