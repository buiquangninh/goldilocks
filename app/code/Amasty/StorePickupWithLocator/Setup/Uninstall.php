<?php

declare(strict_types=1);

namespace Amasty\StorePickupWithLocator\Setup;

use Amasty\StorePickupWithLocator\Model\ResourceModel\Order;
use Amasty\StorePickupWithLocator\Model\ResourceModel\Quote;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->getConnection()
            ->dropTable($setup->getTable(Quote::TABLE));
        $setup->getConnection()
            ->dropTable($setup->getTable(Order::TABLE));
    }
}
