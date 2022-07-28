<?php
declare(strict_types=1);

namespace Amasty\DeliveryDateManager\Block\Adminhtml\Sales\Order\Renderer;

use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Time Component
 */
class Time extends \Magento\Framework\Data\Form\Element\Date
{
    /**
     * @var Json
     */
    private $json;

    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        TimezoneInterface $localeDate,
        Json $json,
        $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $localeDate, $data);
        $this->json = $json;
        $this->localeDate = $localeDate;
    }

    /**
     * @return string
     */
    public function getElementHtml(): string
    {
        $this->addClass('admin__control-text input-text');
        $jsonData = $this->json->serialize(
            [
                'amdeliveryTimepicker' => [
                    'showsDate'  => false,
                    'showsTime'  => true,
                    'timeOnly'   => true,
                    'timeFormat' => $this->localeDate->getTimeFormat(),
                    'closeText'  => __('OK')
                ]
            ]
        );

        $dataInit = 'data-mage-init="' . $this->_escape($jsonData) . '"';

        $html = sprintf(
            '<input name="%s" id="%s" value="%s" %s %s />',
            $this->getName(),
            $this->getHtmlId(),
            $this->_escape($this->getValue()),
            $this->serialize($this->getHtmlAttributes()),
            $dataInit
        );
        $html .= $this->getAfterElementHtml();

        return $html;
    }
}
