<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Api\Data;

interface ScheduleInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    public const ID = 'id';
    public const FEED_ID = 'feed_id';
    public const CRON_TIME = 'cron_time';
    public const CRON_DAY = 'cron_day';
    /**#@-*/

    /**
     * Returns id field
     *
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Returns feed_id field
     *
     * @return int
     */
    public function getFeedId();

    /**
     * @param int $feedId
     *
     * @return $this
     */
    public function setFeedId($feedId);

    /**
     * Returns cron_time field
     *
     * @return int
     */
    public function getCronTime();

    /**
     * @param int $cronTime
     *
     * @return $this
     */
    public function setCronTime($cronTime);

    /**
     * Returns cron_day field
     *
     * @return int
     */
    public function getCronDay();

    /**
     * @param int $cronDay
     *
     * @return $this
     */
    public function setCronDay($cronDay);
}
