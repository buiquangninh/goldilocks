<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Promo
*/


namespace Amasty\Promo\Observer\Quote\Cart\Totals;

use Amasty\Promo\Model\Prefix;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class ItemConverterObserver for items_additional_data event
 */
class ItemConverterObserver implements ObserverInterface
{
    /**
     * @var Prefix
     */
    private $prefix;

    public function __construct(Prefix $prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $item = $observer->getEvent()->getItem();

        if ($this->prefix->isNeedPrefix($item)) {
            $this->prefix->addPrefixToName($item);
        }
    }
}
