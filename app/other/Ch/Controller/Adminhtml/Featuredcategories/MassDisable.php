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

namespace Custom\Chharo\Controller\Adminhtml\Featuredcategories;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Custom\Chharo\Model\ResourceModel\Featuredcategories\CollectionFactory;
use Custom\Chharo\Api\FeaturedcategoriesRepositoryInterface;

/**
 * Class MassDisable.
 */
class MassDisable extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var FeaturedcategoriesRepositoryInterface
     */
    protected $_featuredcategoriesRepository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @param Context                                     $context
     * @param Filter                                      $filter
     * @param CollectionFactory                           $collectionFactory
     * @param FeaturedcategoriesRepositoryInterface       $featuredcategoriesRepository
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        FeaturedcategoriesRepositoryInterface $featuredcategoriesRepository,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_featuredcategoriesRepository = $featuredcategoriesRepository;
        $this->_date = $date;
        parent::__construct($context);
    }

    /**
     * Execute action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     *
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $featuredcategoriessUpdated = 0;
        $coditionArr = [];
        foreach ($collection->getAllIds() as $key => $featuredcategoriesId) {
            $currentFeaturedcategories = $this->_featuredcategoriesRepository->getById(
                $featuredcategoriesId
            );
            $featuredcategoriesData = $currentFeaturedcategories->getData();
            if (count($featuredcategoriesData)) {
                $condition = "`id`=".$featuredcategoriesId;
                array_push($coditionArr, $condition);
                $featuredcategoriessUpdated++;
            }
        }
        $coditionData = implode(' OR ', $coditionArr);

        $collection->setFeaturedcategoriesData(
            $coditionData,
            ['status' => 0, 'updated_at' => $this->_date->gmtDate()]
        );

        if ($featuredcategoriessUpdated) {
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) were disabled.', $featuredcategoriessUpdated)
            );
        }
        /**
 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect 
*/
        return $resultRedirect->setPath('chharo/featuredcategories/index');
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Custom_Chharo::featuredcategories');
    }
}
