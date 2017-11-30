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

namespace Custom\Chharo\Controller\Adminhtml\Bannerimage;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Custom\Chharo\Model\ResourceModel\Bannerimage\CollectionFactory;
use Custom\Chharo\Api\BannerimageRepositoryInterface;

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
     * @var BannerimageRepositoryInterface
     */
    protected $_bannerimageRepository;

    /**
     * @param Context                        $context
     * @param Filter                         $filter
     * @param CollectionFactory              $collectionFactory
     * @param BannerimageRepositoryInterface $bannerimageRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        BannerimageRepositoryInterface $bannerimageRepository
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_bannerimageRepository = $bannerimageRepository;
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
        $bannersDeleted = 0;
        foreach ($collection->getAllIds() as $bannerimageId) {
            if (!empty($bannerimageId)) {
                try {
                    $this->_bannerimageRepository->deleteById($bannerimageId);
                    $this->messageManager->addSuccess(__('Banner has been deleted.'));
                    $bannersDeleted++;
                } catch (\Exception $exception) {
                    $this->messageManager->addError($exception->getMessage());
                }
            }
        }

        if ($bannersDeleted) {
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) were deleted.', $bannersDeleted)
            );
        }
        /**
 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect 
*/
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('chharo/bannerimage/index');
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Custom_Chharo::bannerimage');
    }
}
