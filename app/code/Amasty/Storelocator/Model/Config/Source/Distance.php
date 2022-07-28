<?php

namespace Amasty\Storelocator\Model\Config\Source;

class Distance implements \Magento\Framework\Data\OptionSourceInterface
{

    public function toOptionArray()
    {
        return [
            [
                'value' => 'km',
                'label' => __('Kilometers'),
            ],
            [
                'value' => 'mi',
                'label' => __('Miles'),
            ],
            [
                'value' => 'choose',
                'label' => __('Allow User To Choose'),
            ],
        ];
    }
}
