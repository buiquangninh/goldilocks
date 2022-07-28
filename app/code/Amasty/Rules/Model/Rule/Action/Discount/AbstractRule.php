<?php

namespace Amasty\Rules\Model\Rule\Action\Discount;

use Amasty\Rules\Api\Data\RuleInterface;
use Amasty\Rules\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Rule\Action\Discount;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Base class for all Amasty Rule actions.
 */
abstract class AbstractRule extends Discount\AbstractDiscount
{
    public const ASC_SORT = 'asc';
    public const DESC_SORT = 'desc';
    public const ALL_WEBSITES = '0';

    public const HUNDRED_PERCENT = 100;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Amasty\Rules\Model\Rule\ItemCalculationPrice
     */
    protected $itemPrice;

    /**
     * @var Data
     */
    protected $rulesDataHelper;

    /**
     * @var \Amasty\Rules\Helper\Discount
     */
    protected $rulesDiscountHelper;

    /**
     * @var \Magento\Customer\Model\Session|\Magento\Framework\Session\SessionManager
     */
    protected $customerSession;

    /**
     * @var array|null
     */
    protected $itemsWithDiscount = null;

    /**
     * @var \Amasty\Rules\Model\RuleResolver
     */
    protected $ruleResolver;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoriesCollection;

    /**
     * @var \Amasty\Rules\Model\ConfigModel
     */
    private $configModel;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollections;

    public function __construct(
        \Magento\SalesRule\Model\Validator $validator,
        Discount\DataFactory $discountDataFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        StoreManagerInterface $storeManager,
        \Amasty\Rules\Model\Rule\ItemCalculationPrice $itemPrice,
        Data $rulesDataHelper,
        \Amasty\Rules\Helper\Discount $rulesDiscountHelper,
        \Magento\Framework\Session\SessionManager $customerSession,
        \Amasty\Rules\Model\ConfigModel $configModel,
        \Amasty\Rules\Model\RuleResolver $ruleResolver,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesCollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollections
    ) {
        parent::__construct($validator, $discountDataFactory, $priceCurrency);

        $this->storeManager = $storeManager;
        $this->itemPrice = $itemPrice;
        $this->rulesDataHelper = $rulesDataHelper;
        $this->customerSession = $customerSession;
        $this->rulesDiscountHelper = $rulesDiscountHelper;
        $this->configModel = $configModel;
        $this->ruleResolver = $ruleResolver;
        $this->categoriesCollection = $categoriesCollection;
        $this->productCollections = $productCollections;
    }

    /**
     * @param \Magento\SalesRule\Model\Data\Rule|Rule $rule
     *
     * @return int|null
     * @throws \Exception
     */
    protected function getRuleId($rule)
    {
        return $this->ruleResolver->getLinkId($rule);
    }

    /**
     * Covered by unit-test.
     *
     * @param Address $address
     * @param Rule $rule
     * @param string $order
     *
     * @return AbstractItem[]
     *
     * @throws LocalizedException
     */
    protected function getSortedItems($address, $rule, $order)
    {
        $items = $this->getAllItems($address);
        $items = $this->validateItems($items, $rule);
        $items = $this->splitItemsWithQty($items);
        $items = $this->sortItemsByPrice($items, $order);

        return $items;
    }

    /**
     * Covered by unit-test @param Address $address
     *
     * @return Address\Item[]
     * @see AbstractRule::getSortedItems
     *
     */
    protected function getAllItems($address)
    {
        $items = $address->getAllItems();

        return $items;
    }

    /**
     * @param array $items
     * @param Rule $rule
     *
     * @return AbstractItem[]
     * @throws LocalizedException
     */
    protected function validateItems($items, $rule)
    {
        $validItems = [];
        $amrulesId = 1;

        /** @var Item $item */
        foreach ($items as $item) {
            if ($this->skip($rule, $item) ||
                $item->getParentItem() &&
                $this->isNotBundle($item->getParentItem())) {
                continue;
            }

            if ($this->validator->getItemBasePrice($item) != 0
                && $this->validateItem($item, $rule)
                && !$item->getAmpromoRuleId()
            ) {
                $item->setAmrulesId($amrulesId);
                $validItems[] = $item;
                $amrulesId++;
            }
        }

        return $validItems;
    }

    protected function isNotBundle($item)
    {
        return $item->getProductType() !== 'bundle';
    }

    /**
     * @param Item $item
     * @param Rule $rule
     *
     * @return bool
     */
    private function validateItem($item, $rule)
    {
        if (!$rule->getActions()->validate($item)) {
            $childItems = $item->getChildren();

            if (!empty($childItems)) {
                foreach ($childItems as $childItem) {
                    if ($rule->getActions()->validate($childItem)) {
                        return true;
                    }
                }
            }

            return false;
        }

        return true;
    }

    /**
     * Covered by unit-test @param array $items
     *
     * @return AbstractItem[]
     * @see AbstractRule::getSortedItems
     *
     */
    protected function splitItemsWithQty($items)
    {
        $resItems = [];

        foreach ($items as $item) {
            $qty = $item->getQty();

            for ($i = 0; $i < $qty; $i++) {
                $resItems[] = $item;
            }
        }

        return $resItems;
    }

    /**
     * Covered by unit-test @see AbstractRule::getSortedItems
     *
     * @param \Magento\Quote\Model\Quote\Address\Item[] $items
     * @param string $order
     *
     * @return \Magento\Quote\Model\Quote\Address\Item[]
     */
    protected function sortItemsByPrice($items, $order)
    {
        if ($order == self::ASC_SORT) {
            usort($items, [$this, "ascSort"]);
        }

        if ($order == self::DESC_SORT) {
            usort($items, [$this, "descSort"]);
        }

        return $items;
    }

    /**
     * Covered by unit-test @see AbstractRule::sortItemsByPrice
     *
     * @param Address\Item $item1
     * @param Address\Item $item2
     *
     * @return bool
     */
    protected function ascSort($item1, $item2)
    {
        return $this->validator->getItemBasePrice($item1) <=> $this->validator->getItemBasePrice($item2);
    }

    /**
     * Covered by unit-test @see AbstractRule::sortItemsByPrice
     *
     * @param Address\Item $item1
     * @param Address\Item $item2
     *
     * @return bool
     */
    protected function descSort($item1, $item2)
    {
        return $this->validator->getItemBasePrice($item2) <=> $this->validator->getItemBasePrice($item1);
    }

    /**
     * @param array $items
     *
     * @return array
     */
    protected function getItemsId($items)
    {
        $itemsId = [];

        foreach ($items as $item) {
            $itemsId[] = (int)$item->getAmrulesId();
        }

        return $itemsId;
    }

    /**
     * Covered by unit-test @see AbstractRule::skipEachN
     *
     * @param Rule $rule
     * @param int $step
     * @param int $i
     * @param float $currQty
     * @param float $qty
     * @param null|int $eachNCounter
     *
     * @return bool
     */
    protected function skipBySteps($rule, $step, $i, $currQty, $qty, $eachNCounter = null)
    {
        $simpleAction = $rule->getSimpleAction();

        if ($i === 0 && in_array($simpleAction, Data::TYPE_EACH_M_AFT_N)) {
            return false;
        }
        if ($step > 1 && $eachNCounter % $step && in_array($simpleAction, Data::GROUP_EACH_N)) {
            return true;
        }
        if ($step > 1 && ($i % $step) && in_array($simpleAction, Data::TYPE_EACH_M_AFT_N)) {
            return true;
        }

        $typeGroupN = Data::TYPE_GROUP_N;
        $typeGroupNDisc = Data::TYPE_GROUP_N_DISC;

        // introduce limit for each N with discount or each N with fixed.
        if ((($currQty >= $qty) && ($simpleAction !== $typeGroupN) && ($simpleAction !== $typeGroupNDisc))
            || (($rule->getDiscountQty() <= $currQty) && ($rule->getDiscountQty())
                && (($simpleAction === $typeGroupN)
                    || ($simpleAction === $typeGroupNDisc)))
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param array $array
     * @param float $value
     *
     * @return float
     */
    public function getArrayValueCount($array, $value)
    {
        $values = array_count_values($array);

        return $values[$value];
    }

    /**
     * Covered by unit-test @see AbstractRule::skipEachN
     *
     * @param float $qty
     * @param Rule $rule
     *
     * @return int
     */
    public function ruleQuantity($qty, $rule)
    {
        $discountQty = 1;
        $discountStep = (int)$rule->getDiscountStep();

        if ($discountStep) {
            if (in_array($rule->getSimpleAction(), Data::TYPE_EACH_M_AFT_N)) {
                $discountQty = round($qty / $discountStep);
            } else {
                $discountQty = floor($qty / $discountStep);
            }

            $maxDiscountQty = (int)$rule->getDiscountQty();

            if (!$maxDiscountQty) {
                $maxDiscountQty = $qty;
            }

            $discountQty = min($discountQty, $maxDiscountQty);
        }

        return $discountQty;
    }

    /**
     * Covered by unit-test.
     *
     * @param array $allItems
     * @param Rule $rule
     *
     * @return array
     */
    public function skipEachN($allItems, $rule)
    {
        $step = (int)$rule->getDiscountStep();

        if ($step <= 0) {
            $step = 1;
        }

        $currQty = 0;
        $resItems = [];
        $itemsId = $this->getItemsId($allItems);
        $ruleQty = $this->ruleQuantity(count($itemsId), $rule);
        $eachN = 1;

        foreach ($allItems as $i => $allItem) {
            if ($this->skipBySteps($rule, $step, $i, $currQty, $ruleQty, $eachN)) {
                $eachN++;

                continue;
            }

            $eachN++;
            $currQty++;
            $resItems[] = $allItem;
        }

        return $resItems;
    }

    /**
     * @param AbstractItem $item
     *
     * @return int
     */
    protected function getItemQty($item)
    {
        if (!$item) {
            return 1;
        }

        return $item->getTotalQty() ?: $item->getQty();
    }

    /**
     * @param AbstractItem $item
     *
     * @return int
     */
    protected function getItemQtyBundle($item)
    {
        if (!$item) {
            return 1;
        }

        return $item->getQty() ?: $item->getTotalQty();
    }

    /**
     * @param array $prices
     * @param int $qty
     *
     * @return bool
     */
    public function hasDiscountItems($prices, $qty)
    {
        if (!$prices || $qty < 1) {
            return false;
        }

        return true;
    }

    /**
     * @param Rule $rule
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function beforeCalculate(Rule $rule)
    {
        $specialPromotionRule = $this->ruleResolver->getSpecialPromotions($rule);
        $this->itemPrice->setPriceSelector($specialPromotionRule->getPriceselector());

        return true;
    }

    /**
     * @param Discount\Data $discountData
     * @param Rule $rule
     * @param AbstractItem $item
     *
     * @return bool
     */
    public function afterCalculate($discountData, $rule, $item)
    {
        $this->rulesDiscountHelper->setDiscount(
            $rule,
            $discountData,
            $item->getQuote()->getStore(),
            $item->getId()
        );

        return true;
    }

    /**
     * determines if we should skip the items with special price or other (in futeure) conditions
     *
     * @param Rule $rule
     * @param AbstractItem $item
     *
     * @return bool
     * @throws LocalizedException
     */
    public function skip($rule, $item)
    {
        if ($rule->getSimpleAction() == 'cart_fixed') {
            return false;
        }

        $websiteId = $this->storeManager->getWebsite()->getId();
        $groupId = $this->customerSession->getCustomerGroupId();

        $skipTierPrice = $this->configModel->getSkipTierPrice();

        $origProduct = $item->getProduct();
        $tierPrices = $origProduct->getTierPrice();

        if ($skipTierPrice) {
            foreach ($tierPrices as $tierPrice) {
                if (($tierPrice['cust_group'] == $groupId
                        || \Magento\Customer\Model\GroupManagement::CUST_GROUP_ALL == $tierPrice['cust_group'])
                    && $item->getQty() >= $tierPrice['price_qty']
                    && ($websiteId == $tierPrice['website_id']
                        || self::ALL_WEBSITES == $tierPrice['website_id'])
                ) {
                    return true;
                }
            }
        }

        if ($item->getProductType() == 'bundle') {
            return false;
        }

        if ($this->skipWithDiscount($item)) {
            return true;
        }

        if ($this->checkSkipRule($rule, $item)) {
            return true;
        }

        return false;
    }

    /**
     * determines if we should skip item by skip rule setting
     *
     * @param Rule $rule
     * @param AbstractItem $item
     *
     * @return bool
     */
    protected function checkSkipRule($rule, $item)
    {
        $result = false;
        $itemBaseDiscount = $item->getBaseDiscountAmount();
        $item->setBaseDiscountAmount(0);
        $skipSpecialPrice = $this->configModel->getSkipSpecialPrice();

        switch ($rule->getAmrulesRule()->getData('skip_rule')) {
            case 0:
                if ($skipSpecialPrice && in_array($item->getProductId(), $this->getItemsWithDiscount($item))) {
                    $result = true;
                }

                break;
            case 1:
                if (in_array($item->getProductId(), $this->getItemsWithDiscount($item))) {
                    $result = true;
                }

                break;
            case 3:
                $price = ($item->getDiscountCalculationPrice() !== null)
                    ? $item->getBaseDiscountCalculationPrice() : $item->getBaseCalculationPrice();

                if ($item->getBaseOriginalPrice() > ($price - $item->getBaseDiscountAmount())) {
                    $result = true;
                }

                break;
        }

        $item->setBaseDiscountAmount($itemBaseDiscount);

        return $result;
    }

    /**
     * @param AbstractItem $item
     *
     * @return bool
     */
    protected function skipWithDiscount($item)
    {
        $skipSpecialConfigurable = $this->configModel->getSkipSpecialPriceConfigurable();

        if ($skipSpecialConfigurable
            && $item->getProductType() == "configurable"
            && !empty($this->getItemsWithDiscount($item))
        ) {
            foreach ($item->getChildren() as $child) {
                if (in_array($child->getProductId(), $this->getItemsWithDiscount($item))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param AbstractItem $item
     *
     * @return int[]
     */
    protected function getItemsWithDiscount($item)
    {
        if ($this->itemsWithDiscount === null) {
            $productIds = $this->itemsWithDiscount = [];
            $address = $item->getAddress();

            foreach ($this->getAllItems($address) as $addressItem) {
                $productIds[] = $addressItem->getProductId();
            }

            if (!$productIds) {
                return $this->itemsWithDiscount;
            }

            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
            $productCollection = $this->productCollections->create();

            $productsCollection = $productCollection
                ->addPriceData()
                ->addAttributeToFilter('entity_id', ['in' => $productIds]);

            /** @var \Magento\Catalog\Model\Product $product */
            foreach ($productsCollection->getItems() as $product) {
                $product->setPriceCalculation($product->getDataByKey('final_price') === null);
                if ($product->getPrice() > $product->getFinalPrice()) {
                    $this->itemsWithDiscount[] = $product->getId();
                }
                unset($product);
            }
        }

        return $this->itemsWithDiscount;
    }

    /**
     * @param AbstractItem $item
     * @param array $itemsId
     * @param AbstractItem $allItem
     * @param int|float $qty
     *
     * @return bool
     */
    protected function isContinueEachmaftnCalculation($item, $itemsId, $allItem, $qty)
    {
        return in_array($item->getAmrulesId(), $itemsId) && $allItem->getAmrulesId() === $item->getAmrulesId()
            && $qty > 0;
    }

    /**
     * @param Rule $rule
     * @param string $defaultSortOrder
     *
     * @return string
     */
    public function getSortOrder($rule, $defaultSortOrder)
    {
        $amRule = $rule->getAmrulesRule();
        if ($amRule) {
            $order = $amRule->getApplyDiscountTo() ? $amRule->getApplyDiscountTo() : $defaultSortOrder;
        } else {
            $order = self::ASC_SORT;
        }

        return $order;
    }

    /**
     * @param Discount\Data $discountData
     * @param AbstractItem $item
     * @param float $itemPrice
     * @param float $itemBasePrice
     * @param float $discountCoefficient
     * @param float $itemQty
     * @return Discount\Data
     */
    protected function calculateDiscountByCoefficient(
        Discount\Data $discountData,
        AbstractItem $item,
        float $itemPrice,
        float $itemBasePrice,
        float $discountCoefficient,
        float $itemQty
    ): Discount\Data {
        $discountAmount = $itemPrice * $discountCoefficient;
        $baseDiscountAmount = $itemBasePrice * $discountCoefficient;
        $itemOriginalPrice = $this->validator->getItemOriginalPrice($item);
        $baseItemOriginalPrice = $this->validator->getItemBaseOriginalPrice($item);

        $discountAmount = $this->itemPrice->resolveFinalPriceRevert($discountAmount, $item);
        $baseDiscountAmount = $this->itemPrice->resolveBaseFinalPriceRevert($baseDiscountAmount, $item);

        $discountData->setAmount($itemQty * $discountAmount);
        $discountData->setBaseAmount($itemQty * $baseDiscountAmount);
        $discountData->setOriginalAmount($itemQty * $itemOriginalPrice * $discountCoefficient);
        $discountData->setBaseOriginalAmount($itemQty * $baseItemOriginalPrice * $discountCoefficient);

        return $discountData;
    }

    /**
     * @param AbstractItem[] $allItems
     * @return float|int
     */
    protected function getBaseSumOfItems(array $allItems)
    {
        $baseSum = 0;
        /** @var AbstractItem $allItem */
        foreach ($allItems as $allItem) {
            $baseSum += $this->validator->getItemBasePrice($allItem);
        }

        return $baseSum;
    }

    /**
     * @param AbstractItem[] $allItems
     * @return float|int
     */
    protected function getBaseOriginalSumOfItems(array $allItems)
    {
        $baseSum = 0;
        /** @var AbstractItem $allItem */
        foreach ($allItems as $allItem) {
            $baseSum += $this->validator->getItemBaseOriginalPrice($allItem);
        }

        return $baseSum;
    }
}
