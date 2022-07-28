<?php

namespace Magenest\GoogleTagManager\Model\DataCollectors\ProductObject;

use Magento\Framework\DataObjectFactory;
use Magento\Framework\Event\ManagerInterface;
use Magenest\GoogleTagManager\Api\ProductObjectCollectorInterface;

class QuoteItem implements ProductObjectCollectorInterface
{
    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    public function __construct(
        ManagerInterface $eventManager,
        DataObjectFactory $dataObjectFactory
    ) {
        $this->eventManager = $eventManager;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    public function collect($fromObject, array $baseData = [])
    {
        if (!$fromObject instanceof \Magento\Quote\Model\Quote\Item) {
            return $baseData;
        }

        $dataObject = $this->dataObjectFactory->create(['data' => $baseData]);
        $this->eventManager->dispatch('gtm_populate_product_object_from_quote_item', [
            'quote_item' => $fromObject,
            'data' => $dataObject,
        ]);

        return $dataObject->getData();
    }
}
