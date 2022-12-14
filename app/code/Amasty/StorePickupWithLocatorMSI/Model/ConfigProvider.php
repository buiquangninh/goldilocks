<?php

declare(strict_types=1);

namespace Amasty\StorePickupWithLocatorMSI\Model;

use Amasty\Base\Model\ConfigProviderAbstract;
use Amasty\StorePickupWithLocator\Model\ConfigProvider as PickupConfigProvider;

class ConfigProvider extends ConfigProviderAbstract
{
    public const SHOW_LOCATIONS_WITH_MSI = 'show_locations_with_msi';
    public const INCLUDE_OUT_OF_STOCK_LOCATIONS = 'include_out_of_stock_locations';

    /**
     * @var string
     */
    protected $pathPrefix = 'storepickup_locator/';

    /**
     * @return bool
     */
    public function isShowLocationsWithMsi(): bool
    {
        return $this->isSetFlag(PickupConfigProvider::GENERAL_BLOCK . self::SHOW_LOCATIONS_WITH_MSI);
    }

    /**
     * @return bool
     */
    public function isIncludeOutOfStockLocations(): bool
    {
        return $this->isSetFlag(PickupConfigProvider::GENERAL_BLOCK . self::INCLUDE_OUT_OF_STOCK_LOCATIONS);
    }
}
