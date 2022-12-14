<?php
declare(strict_types=1);

namespace Amasty\Rolepermissions\Plugin\Customer\Model\ResourceModel\Grid;

use Magento\Customer\Model\ResourceModel\Grid\Collection;

class CollectionPlugin
{
    public function beforeAddFieldToFilter(Collection $subject, $field, $condition): ?array
    {
        if (is_string($field)
            && (strpos($field, '.') === false)
            && $subject->getConnection()->tableColumnExists($subject->getMainTable(), $field)
        ) {
            return ['main_table.' . $field, $condition];
        }

        return null;
    }
}
