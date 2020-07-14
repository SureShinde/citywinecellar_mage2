<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Model\Adminhtml\ScheduleTime;
/**
 * Class Comment
 * @package Magestore\ReportSuccess\Model\Adminhtml\ScheduleTime
 */
class Comment implements \Magento\Config\Model\Config\CommentInterface
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * Comment constructor.
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Magento\Framework\UrlInterface $url
    ){
        $this->url = $url;
    }

    /**
     * @param string $elementValue
     * @return \Magento\Framework\Phrase|string
     */
    public function getCommentText($elementValue)
    {
        $link = $this->url->getUrl('omcreports/inventory/historicalStock');
        return __('Schedule time on a daily basis to auto generate new Historical Stock Reports. To get a report before this time, admin can use the manual action on the <a title=\"Historical Stock Report page\" target=\'_blank\' href=%1>Historical Stock Report page</a>.', $link);
    }
}