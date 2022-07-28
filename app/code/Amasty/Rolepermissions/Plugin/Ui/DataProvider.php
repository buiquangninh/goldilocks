<?php

namespace Amasty\Rolepermissions\Plugin\Ui;

use Magento\Framework\Api\SearchResultsInterface;

class DataProvider
{
    /**
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider $subject
     * @param SearchResultsInterface $result
     * @return SearchResultsInterface
     */
    public function afterGetSearchResult(
        \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider $subject,
        SearchResultsInterface $result
    ) {
        $result->getItems(); // Force collection load before getTotalCount() call

        return $result;
    }
}
