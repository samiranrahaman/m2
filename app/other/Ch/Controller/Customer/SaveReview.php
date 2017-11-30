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
class SaveReview extends \Custom\Chharo\Controller\ApiController
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
            $storeId = $this->getRequest()->getPost("storeId");
            $id = $this->getRequest()->getPost("id");
            $title = $this->getRequest()->getPost("title");
            $detail = $this->getRequest()->getPost("detail");
            $nickname = $this->getRequest()->getPost("nickname");
            $customerId = $this->getRequest()->getPost("customerId");
            if ($storeId == "") {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                if (!$customerId > 0) {
                    $customerId = null;
                }
                $ratingsObj = $this->getRequest()->getPost("ratings");
                $ratingsObj = json_encode(
                    ["rating" => 5]
                );
                $ratingsObj = json_decode($ratingsObj);
                $ratings = [];
                foreach ($ratingsObj as $key => $value) {
                    $ratings[$key] = $value;
                }
                $review = $this->_objectManager
                    ->create("\Magento\Review\Model\Review");
                $review->setEntityPkValue($id);
                $review->setStatusId(\Magento\Review\Model\Review::STATUS_PENDING);
                $review->setTitle($title);
                $review->setDetail($detail);
                $review->setEntityId(1);
                $review->setStoreId($storeId);
                $review->setCustomerId($customerId);
                $review->setNickname($nickname);
                $review->setReviewId($review->getId());
                $review->setStores([$storeId]);
                $review->save();
                foreach ($ratings as $ratingId => $optionId) {
                    $this->_objectManager
                        ->create("\Magento\Review\Model\Rating")
                        ->setRatingId($ratingId)
                        ->setReviewId($review->getId())
                        ->setCustomerId($customerId)
                        ->addOptionVote($optionId, $id);
                }
                $review->aggregate();
                $returnArray["message"] =__("Your review has been accepted for moderation.");

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
