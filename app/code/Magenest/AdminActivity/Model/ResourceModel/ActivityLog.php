<?php

namespace Magenest\AdminActivity\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class ActivityLog
 *
 * @package Magenest\AdminActivity\Model\ResourceModel
 */
class ActivityLog extends AbstractDb
{
	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	public function _construct()
	{
		// Table Name and Primary Key column
		$this->_init('magenest_activity_log', 'entity_id');
	}
}
