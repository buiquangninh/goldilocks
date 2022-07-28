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

declare(strict_types=1);

namespace Plumrocket\PrivateSale\Block\Adminhtml\Event\Helper\Form;

class Gallery extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery
{
    /**
     * @var string
     */
    protected $fieldNameSuffix = 'event';

    /**
     * @var string
     */
    protected $name = 'event[event_video]';

    /**
     * @var string
     */
    protected $formName = 'prprivatesale_event_form';

    /**
     * @return array|null
     */
    public function getImages()
    {
        return null;
    }
}
