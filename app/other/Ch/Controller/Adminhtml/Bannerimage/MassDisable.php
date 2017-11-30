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
     * @var BannerimageRepositoryInterface
     */
    protected $_bannerimageRepository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @param Context                                     $context
     * @param Filter                                      $filter
     * @param CollectionFactory                           $collectionFactory
     * @param BannerimageRepositoryInterface              $bannerimageRepository
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        BannerimageRepositoryInterface $bannerimageRepository,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_bannerimageRepository = $bannerimageRepository;
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
        $bannersUpdated = 0;
        $coditionArr = [];
        foreach ($collection->getAllIds() as $key => $bannerimageId) {
            $currentBanner = $this->_bannerimageRepository->getById($bannerimageId);
            $bannerimageData = $currentBanner->getData();
            if (count($bannerimageData)) {
                $condition = "`id`=".$bannerimageId;
                array_push($coditionArr, $condition);
                $bannersUpdated++;
            }
        }
        $coditionData = implode(' OR ', $coditionArr);

        $collection->setBannerimageData(
            $coditionData,
            ['status' => 0, 'updated_at' => $this->_date->gmtDate()]
        );

        if ($bannersUpdated) {
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) were disabled.', $bannersUpdated)
            );
        }
        /**
 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect 
*/
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
