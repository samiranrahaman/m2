<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

/**
 * Directory Country Resource Collection
 */
namespace Addsubscriptionplans\Gridpartsubscriptionplans\Model\ResourceModel\Template;

/**
 * Class Collection
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'subscriptionplanid';
    
    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Addsubscriptionplans\Gridpartsubscriptionplans\Model\Template', 'Addsubscriptionplans\Gridpartsubscriptionplans\Model\ResourceModel\Template');
    }

    
}
