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

namespace Custom\Chharo\Controller\Adminhtml\Categoryimages;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Custom\Chharo\Model\ResourceModel\Categoryimages\CollectionFactory;
use Custom\Chharo\Api\CategoryimagesRepositoryInterface;

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
     * @var CategoryimagesRepositoryInterface
     */
    protected $_categoryimagesRepository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @param Context                                     $context
     * @param Filter                                      $filter
     * @param CollectionFactory                           $collectionFactory
     * @param CategoryimagesRepositoryInterface           $categoryimagesRepository
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CategoryimagesRepositoryInterface $categoryimagesRepository,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_categoryimagesRepository = $categoryimagesRepository;
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
        $categoryimagessUpdated = 0;
        $coditionArr = [];
        foreach ($collection->getAllIds() as $key => $categoryimagesId) {
            $currentCategoryimages = $this->_categoryimagesRepository->getById(
                $categoryimagesId
            );
            $categoryimagesData = $currentCategoryimages->getData();
            if (count($categoryimagesData)) {
                $condition = "`id`=".$categoryimagesId;
                array_push($coditionArr, $condition);
                $categoryimagessUpdated++;
            }
        }
        $coditionData = implode(' OR ', $coditionArr);

        $collection->setCategoryimagesData(
            $coditionData,
            ['status' => 0, 'updated_at' => $this->_date->gmtDate()]
        );

        if ($categoryimagessUpdated) {
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) were disabled.', $categoryimagessUpdated)
            );
        }
        /**
 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect 
*/
        return $resultRedirect->setPath('chharo/categoryimages/index');
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Custom_Chharo::categoryimages');
    }
}
