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

namespace Custom\Chharo\Model\Featuredcategories;

use Magento\Eav\Model\Config;
use Custom\Chharo\Model\Featuredcategories;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Session\SessionManagerInterface;
use Custom\Chharo\Model\ResourceModel\Featuredcategories\Collection;
use Custom\Chharo\Model\ResourceModel\Featuredcategories\CollectionFactory as FeaturedcatCollectionFactory;

/**
 * Class DataProvider.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var SessionManagerInterface
     */
    protected $session;

    /**
     * Constructor.
     *
     * @param string                       $name
     * @param string                       $primaryFieldName
     * @param string                       $requestFieldName
     * @param FeaturedcatCollectionFactory $featuredcategoriesCollectionFactory
     * @param array                        $meta
     * @param array                        $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        FeaturedcatCollectionFactory $featuredcategoriesCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $featuredcategoriesCollectionFactory->create();
        $this->collection->addFieldToSelect('*');
    }

    /**
     * Get session object.
     *
     * @return SessionManagerInterface
     */
    protected function getSession()
    {
        if ($this->session === null) {
            $this->session = ObjectManager::getInstance()->get(
                'Magento\Framework\Session\SessionManagerInterface'
            );
        }

        return $this->session;
    }

    /**
     * Get data.
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /**
 * @var Customer $featuredcategories 
*/
        foreach ($items as $featuredcategories) {
            $result['featuredcategories'] = $featuredcategories->getData();
            $this->loadedData[$featuredcategories->getId()] = $result;
        }

        $data = $this->getSession()->getFeaturedcategoriesFormData();
        if (!empty($data)) {
            $featuredcategoriesId = isset($data['chharo_featuredcategories']['id'])
            ? $data['chharo_featuredcategories']['id'] : null;
            $this->loadedData[$featuredcategoriesId] = $data;
            $this->getSession()->unsFeaturedcategoriesFormData();
        }

        return $this->loadedData;
    }
}
