<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Private Sales and Flash Sales
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\PrivateSale\Api;

use Plumrocket\PrivateSale\Model\Config\Source\EventStatus;

/**
 * @since 5.0.0
 */
interface GetEventIdByCategoryIdInterface
{
    /**
     * @param int  $categoryId
     * @param int  $status one of the \Plumrocket\PrivateSale\Model\Config\Source\EventStatus
     * @param bool $forceUpdate
     * @return int
     */
    public function execute(int $categoryId, int $status = EventStatus::ACTIVE, bool $forceUpdate = false): int;
}
