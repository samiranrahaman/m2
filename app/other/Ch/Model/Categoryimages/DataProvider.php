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

namespace Custom\Chharo\Model\Categoryimages;

use Magento\Eav\Model\Config;
use Custom\Chharo\Model\Categoryimages;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Session\SessionManagerInterface;
use Custom\Chharo\Model\ResourceModel\Categoryimages\Collection;
use Custom\Chharo\Model\ResourceModel\Categoryimages\CollectionFactory as CategoryCollectionFactory;

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
     * @param string                    $name
     * @param string                    $primaryFieldName
     * @param string                    $requestFieldName
     * @param CategoryCollectionFactory $categoryimagesCollectionFactory
     * @param array                     $meta
     * @param array                     $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CategoryCollectionFactory $categoryimagesCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $categoryimagesCollectionFactory->create();
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
 * @var Customer $categoryimages 
*/
        foreach ($items as $categoryimages) {
            $result['categoryimages'] = $categoryimages->getData();
            $this->loadedData[$categoryimages->getId()] = $result;
        }

        $data = $this->getSession()->getCategoryimagesFormData();
        if (!empty($data)) {
            $categoryimagesId = isset($data['chharo_categoryimages']['id'])
            ? $data['chharo_categoryimages']['id'] : null;
            $this->loadedData[$categoryimagesId] = $data;
            $this->getSession()->unsCategoryimagesFormData();
        }

        return $this->loadedData;
    }
}
