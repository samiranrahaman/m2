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
class GetReviewDetail extends \Custom\Chharo\Controller\ApiController
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
            $reviewId = $this->getRequest()->getPost("reviewId");
            $width = $this->getRequest()->getPost("width");
            $storeId = $this->getRequest()->getPost("storeId");
            $returnArray = [];
            $ratingArray = [];
            if ($storeId == "") {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                $_review = $this->_objectManager
                    ->create("\Magento\Review\Model\Review")
                    ->load($reviewId);
                $_product = $this->_objectManager
                    ->create("\Magento\Catalog\Model\Product")
                    ->setStoreId($storeId)
                    ->load($_review->getEntityPkValue());

                $returnArray["name"] = $this->_helperCatalog
                    ->stripTags($_product->getName());

                $returnArray["image"] = $this->_helperCatalog
                    ->getImageUrl($_product, $width/2);

                $ratingCollection =$this->_objectManager
                    ->create("\Magento\Review\Model\Rating\Option\Vote")
                    ->getResourceCollection()
                    ->setReviewFilter($reviewId)
                    ->addRatingInfo($storeId)
                    ->setStoreFilter($storeId)
                    ->load();

                foreach ($ratingCollection as $_rating) {
                    $eachRating = [];
                    $eachRating["ratingCode"] = $this->_helperCatalog
                        ->stripTags($_rating->getRatingCode());
                    $eachRating["ratingValue"] = number_format($_rating->getPercent(), 2, ".", "");
                    $ratingArray[] = $eachRating;
                }
                $returnArray["ratingData"] = $ratingArray;
                $returnArray["reviewDate"] = __(
                    "Your Review (submitted on %1)",
                    $this->_helperCatalog->formatDate(
                        $_review->getCreatedAt(),
                        "long"
                    )
                );
                $returnArray["reviewDetail"] = $this->_helperCatalog
                    ->stripTags($_review->getDetail());
                $reviews = $this->_objectManager
                    ->create("\Magento\Review\Model\Review")
                    ->getResourceCollection()
                    ->addStoreFilter($storeId)
                    ->addEntityFilter("product", $_product->getId())
                    ->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED)
                    ->setDateOrder()->addRateVotes();
                $ratings = [];
                if (count($reviews) > 0) {
                    foreach ($reviews->getItems() as $review) {
                        foreach ($review->getRatingVotes() as $vote) {
                            $ratings[] = $vote->getPercent();
                        }
                    }
                }
                if (count($ratings) > 0) {
                    $rating = number_format((5*(array_sum($ratings) / count($ratings)))/100, 2, ".", "");
                } else {
                    $rating = 0;
                }
                $returnArray["rating"] = $rating;
                $returnArray["success"] = 1;
                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $returnArray["success"] = 0;
                $returnArray["message"] = __($e->getMessage());
                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray["success"] = 0;
            $returnArray["message"] = __("Invalid Request.");
            return $this->getJsonResponse($returnArray);
        }
    }
}
