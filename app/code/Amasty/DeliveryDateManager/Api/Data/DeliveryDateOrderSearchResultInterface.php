<?php
declare(strict_types=1);

namespace Amasty\DeliveryDateManager\Api\Data;

/**
 * Search results of getList method of DeliveryDateOrder
 */
interface DeliveryDateOrderSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get source items list
     *
     * @return \Amasty\DeliveryDateManager\Api\Data\DeliveryDateOrderInterface[]
     */
    public function getItems();

    /**
     * Set source items list
     *
     * @param \Amasty\DeliveryDateManager\Api\Data\DeliveryDateOrderInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
