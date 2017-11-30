<?php
/**
 * Custom Software.
 *
 * @category  Custom
 * @package   Custom_Chharo
 * @author    Custom
 * @copyright Copyright (c) 2010-2017 Custom Software Private Limited (https://Custom.com)
 * @license   https://store.Custom.com/license.html
 */

namespace Custom\Chharo\Model\ResourceModel\Categoryimages;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Custom Chharo ResourceModel Categoryimages collection.
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $loggerInterface
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategyInterface
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManagerInterface
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null          $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null    $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $loggerInterface,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategyInterface,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $loggerInterface,
            $fetchStrategyInterface,
            $eventManager,
            $connection,
            $resource
        );
    }

    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init(
            'Custom\Chharo\Model\Categoryimages',
            'Custom\Chharo\Model\ResourceModel\Categoryimages'
        );
        $this->_map['fields']['id'] = 'main_table.id';
    }

    /**
     * Add filter by store.
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool                                 $withAdmin
     *
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }

        return $this;
    }

    /**
     * Set Categoryimages data for given condition.
     *
     * @param array $condition
     * @param array $attributeData
     */
    public function setCategoryimagesData($condition, $attributeData)
    {
        return $this->getConnection()->update(
            $this->getTable('chharo_categoryimages'),
            $attributeData,
            $where = $condition
        );
    }
}
