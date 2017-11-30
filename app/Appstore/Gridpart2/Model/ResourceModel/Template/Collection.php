<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

/**
 * Directory Country Resource Collection
 */
namespace Appstore\Gridpart2\Model\ResourceModel\Template;

/**
 * Class Collection
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'gridpart2template_id';
    
    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Appstore\Gridpart2\Model\Template', 'Appstore\Gridpart2\Model\ResourceModel\Template');
    }

    
}
