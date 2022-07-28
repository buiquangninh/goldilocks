<?php

declare(strict_types=1);

namespace Amasty\PreOrderAnalytic\Block\Adminhtml\System\Config\Element;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement as AbstractElement;

class Multiselect extends Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $countOptions = count($element->getValues());
        $element->setData('size', $countOptions ?: 10);

        return $element->getElementHtml();
    }
}
