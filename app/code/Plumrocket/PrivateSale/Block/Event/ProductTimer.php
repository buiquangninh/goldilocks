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
 * @package     Plumrocket Private Sales and Flash Sales v4.x.x
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\PrivateSale\Block\Event;

use Plumrocket\PrivateSale\Model\Config\Source\ProductEventHeaderStyle;

class ProductTimer extends Product
{
    /**
     * @return bool
     */
    public function displayCountdown(): bool
    {
        if ($this->eventHeaderStyle->getProductHeaderType() === ProductEventHeaderStyle::TEMPLATE_3) {
            return parent::displayCountdown();
        }

        return false;
    }
}
