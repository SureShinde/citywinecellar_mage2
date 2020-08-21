<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Model\Statistics;

/**
 * Class PosStatistic
 *
 * Used to create Pos Statistic
 */
class PosStatistic implements StatisticInterface
{

    /**
     * @var string
     */
    protected $statisticId = "";

    /**
     * @var string
     */
    protected $statisticTitle = "";

    /**
     * @var string
     */
    protected $statisticComment = "";

    /**
     * PosReport constructor.
     *
     * @param int $id
     * @param string $title
     * @param string $comment
     */
    public function __construct(
        $id,
        $title,
        $comment = ""
    ) {
        $this->statisticId = $id;
        $this->statisticTitle = $title;
        $this->statisticComment = $comment;
    }

    /**
     * Get statistic id
     *
     * @return string
     */
    public function getStatisticId()
    {
        return $this->statisticId;
    }

    /**
     * Get statistic title
     *
     * @return string
     */
    public function getStatisticTitle()
    {
        return $this->statisticTitle;
    }

    /**
     * Get statistic comment
     *
     * @return string
     */
    public function getStatisticComment()
    {
        return $this->statisticComment;
    }
}
