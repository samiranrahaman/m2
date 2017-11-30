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

namespace Custom\Chharo\Controller\Customer;

use Magento\Framework\App\Action\Context;
use Custom\Chharo\Helper\Data as HelperData;
use Custom\Chharo\Helper\Catalog as HelperCatalog;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\App\Emulation;

/**
 * Chharo API Customer controller.
 */
class GetProReviews extends \Custom\Chharo\Controller\ApiController
{

    /**
     * $_customerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        HelperData $helper,
        HelperCatalog $helperCatalog,
        Emulation $emulate,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
    
        $this->_customerFactory = $customerFactory;
        parent::__construct($context, $helper, $helperCatalog, $emulate);
    }

    /**
     * execute category list
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $customerEmail = $this->getRequest()->getPost("customerEmail");
            $storeId = $this->getRequest()->getPost("storeId");
            $websiteId = $this->getRequest()->getPost("websiteId");

            try {
                $customer = $this->_customerFactory->create()
                    ->setWebsiteId($websiteId)
                    ->loadByEmail($customerEmail);
                $returnArray = [];
                $reviews =  $this->_objectManager
                    ->create("\Magento\Review\Model\Review")
                    ->getProductCollection()
                    ->addStoreFilter($storeId)
                    ->addCustomerFilter($customer->getId())
                    ->setDateOrder();

                $pageNumber = $this->getRequest()->getPost("pageNumber");
                if ($pageNumber != "") {
                    $pageNumber = $this->getRequest()->getPost("pageNumber");
                    $returnArray["totalCount"] = $reviews->getSize();
                    $reviews->setPageSize(16)->setCurPage($pageNumber);
                }
                $allReviews = [];

                foreach ($reviews as $key => $_review) {
                    $eachReview = [];
                    $eachReview["date"] = $this->_helperCatalog
                        ->formatDate(
                            $_review->getReviewCreatedAt(),
                            "short"
                        );
                    $eachReview["id"] = $key;
                    $width = $this->getRequest()->getPost("width");
                    if ($width != "") {
                        $_product = $this->_objectManager
                            ->create("\Magento\Catalog\Model\Product")
                            ->load($_review->getId());
                        $eachReview["thumbNail"] =
                        $this->_helperCatalog
                            ->getImageUrl($_product, $width/3);
                    }
                    $eachReview["typeId"] = $_review->getTypeId();
                    $eachReview["productId"] = $_review->getId();
                    $eachReview["proName"] = $this->_helperCatalog->stripTags($_review->getName());
                    $eachReview["details"] =
                    $this->_helperCatalog
                        ->coreString->strlen($_review->getDetail()) > 50 ?
                    $this->_helperCatalog
                        ->coreString->substr(
                            $this->_helperCatalog->stripTags(
                                $_review->getDetail()
                            ),
                            50
                        ): $this->_helperCatalog->stripTags(
                            $_review->getDetail()
                        );
                    $allReviews[] = $eachReview;
                }
                $returnArray["allReviews"] = $allReviews;
                $returnArray["success"] = 1;
                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray["success"] = 0;
                $returnArray["message"] = __("Invalid Request.");
                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray["success"] = 0;
            $returnArray["message"] = __("Invalid Request.");
            return $this->getJsonResponse($returnArray);
        }
    }
}
