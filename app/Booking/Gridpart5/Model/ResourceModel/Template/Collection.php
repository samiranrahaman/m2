<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

/**
 * Directory Country Resource Collection
 */
namespace Booking\Gridpart5\Model\ResourceModel\Template;

/**
 * Class Collection
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
   // protected $_idFieldName = 'entity_id';
    protected $_idFieldName = 'item_id';
    
    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Booking\Gridpart5\Model\Template', 'Booking\Gridpart5\Model\ResourceModel\Template');
    }

    
}
