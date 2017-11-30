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
     * @var FeaturedcategoriesRepositoryInterface
     */
    protected $_featuredcategoriesRepository;

    /**
     * @param Context                               $context
     * @param Filter                                $filter
     * @param CollectionFactory                     $collectionFactory
     * @param FeaturedcategoriesRepositoryInterface $featuredcategoriesRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        FeaturedcategoriesRepositoryInterface $featuredcategoriesRepository
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_featuredcategoriesRepository = $featuredcategoriesRepository;
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
        $featuredcategoriessDeleted = 0;
        foreach ($collection->getAllIds() as $featuredcategoriesId) {
            if (!empty($featuredcategoriesId)) {
                try {
                    $this->_featuredcategoriesRepository->deleteById($featuredcategoriesId);
                    $featuredcategoriessDeleted++;
                } catch (\Exception $exception) {
                    $this->messageManager->addError($exception->getMessage());
                }
            }
        }

        if ($featuredcategoriessDeleted) {
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) were deleted.', $featuredcategoriessDeleted)
            );
        }
        /**
 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect 
*/
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
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
