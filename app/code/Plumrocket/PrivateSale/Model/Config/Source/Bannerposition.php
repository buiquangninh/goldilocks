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

namespace Plumrocket\PrivateSale\Model\Config\Source;

class Bannerposition extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Position Top
     */
    const TOP = 0;

    /**
     * Position Bottom
     */
    const BOTTOM = 1;

    /**
     * Retrieve all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $options = [
            [
                'label' => __('Top'),
                'value' => self::TOP
            ],
            [
                'label' => __('Bottom'),
                'value' => self::BOTTOM
            ]
        ];

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
