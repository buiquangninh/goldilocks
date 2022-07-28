<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Private Sales and Flash Sales v4.x.x
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\PrivateSale\Model\ResourceModel\Event\Attribute;

use Plumrocket\PrivateSale\Model\Event;

class Collection extends \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection
{
    /**
     * Entity factory
     *
     * @var \Magento\Eav\Model\EntityFactory
     */
    public $eavEntityFactory;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->eavEntityFactory = $eavEntityFactory;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $eavConfig, $connection, $resource);
    }

    /**
     * Resource model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Plumrocket\PrivateSale\Model\Event\Attribute::class,
            \Magento\Eav\Model\ResourceModel\Entity\Attribute::class
        );
    }

    /**
     * @inheritDoc
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(
            ['main_table' => $this->getResource()->getMainTable()]
        )->where(
            'main_table.entity_type_id=?',
            $this->eavEntityFactory->create()->setType(Event::ENTITY)->getTypeId()
        )->join(
            ['additional_table' => $this->getTable(Event::ENTITY . '_eav_attribute')],
            'additional_table.attribute_id = main_table.attribute_id'
        );

        return $this;
    }

    /**
     * Specify attribute entity type filter
     *
     * @param int $typeId
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setEntityTypeFilter($typeId)
    {
        return $this;
    }
}
