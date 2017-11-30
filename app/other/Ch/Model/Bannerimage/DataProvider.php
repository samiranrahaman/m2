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

namespace Custom\Chharo\Model\Bannerimage;

use Magento\Eav\Model\Config;
use Custom\Chharo\Model\Bannerimage;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Session\SessionManagerInterface;
use Custom\Chharo\Model\ResourceModel\Bannerimage\Collection;
use Custom\Chharo\Model\ResourceModel\Bannerimage\CollectionFactory as BannerimageCollectionFactory;

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
     * @param BannerimageCollectionFactory $bannerimageCollectionFactory
     * @param array                        $meta
     * @param array                        $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        BannerimageCollectionFactory $bannerimageCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $bannerimageCollectionFactory->create();
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
 * @var Customer $bannerimage 
*/
        foreach ($items as $bannerimage) {
            $result['bannerimage'] = $bannerimage->getData();
            $this->loadedData[$bannerimage->getId()] = $result;
        }

        $data = $this->getSession()->getBannerimageFormData();
        if (!empty($data)) {
            $bannerimageId = isset($data['chharo_bannerimage']['id'])
            ? $data['chharo_bannerimage']['id'] : null;
            $this->loadedData[$bannerimageId] = $data;
            $this->getSession()->unsBannerimageFormData();
        }

        return $this->loadedData;
    }
}
