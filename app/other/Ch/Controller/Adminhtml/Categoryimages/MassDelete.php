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
 * Class MassDelete.
 */
class MassDelete extends \Magento\Backend\App\Action
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
     * @param Context                           $context
     * @param Filter                            $filter
     * @param CollectionFactory                 $collectionFactory
     * @param CategoryimagesRepositoryInterface $categoryimagesRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CategoryimagesRepositoryInterface $categoryimagesRepository
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_categoryimagesRepository = $categoryimagesRepository;
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
        $categoryimagessDeleted = 0;
        foreach ($collection->getAllIds() as $categoryimagesId) {
            if (!empty($categoryimagesId)) {
                try {
                    $this->_categoryimagesRepository->deleteById($categoryimagesId);
                    $categoryimagessDeleted++;
                } catch (\Exception $exception) {
                    $this->messageManager->addError($exception->getMessage());
                }
            }
        }

        if ($categoryimagessDeleted) {
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) were deleted.', $categoryimagessDeleted)
            );
        }
        /**
 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect 
*/
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
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
