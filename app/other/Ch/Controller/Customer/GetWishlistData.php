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
class GetWishlistData extends \Custom\Chharo\Controller\ApiController
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
            $websiteId = $this->getRequest()->getPost("websiteId");
            $storeId = $this->getRequest()->getPost("storeId");
            $width = $this->getRequest()->getPost("width");
            if ($storeId == "") {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                $customer =  $this->_customerFactory->create()
                    ->setWebsiteId($websiteId)
                    ->loadByEmail($customerEmail);
                $returnArray = [];
                $wishlist = $this->_objectManager
                    ->create("\Magento\Wishlist\Model\Wishlist")
                    ->loadByCustomerId($customer->getId(), true);

                $wishListItemCollection = $wishlist->getItemCollection();

                $pageNumber = $this->getRequest()->getPost("pageNumber");
                if ($pageNumber != "") {
                    $pageNumber = $this->getRequest()->getPost("pageNumber");
                    $returnArray["totalCount"] = $wishListItemCollection->getSize();
                    $wishListItemCollection->setPageSize(16)->setCurPage($pageNumber);
                }
                $wishlistData = [];
                foreach ($wishListItemCollection as $item) {
                    $_product = $this->_objectManager
                        ->create('\Magento\Catalog\Model\Product')
                        ->load($item->getProductId());

                    $eachWishData = [];
                    $eachWishData["id"] = $item->getId();
                    $eachWishData["name"] = $item->getProduct()->getName();
                    $eachWishData["description"] = $item->getDescription();
                    $eachWishData["sku"] = $item->getProduct()->getSku();
                    $eachWishData["productId"] = $item->getProduct()->getId();
                    $eachWishData["typeId"] = $item->getProduct()->getTypeId();
                    $eachWishData["qty"] = $item->getQty()*1;
                    $eachWishData["price"] = $this->_helperCatalog->stripTags(
                        $this->_objectManager
                            ->create('Magento\Framework\Pricing\Helper\Data')
                            ->currency($item->getProduct()->getFinalPrice())
                    );
                   
                    $eachWishData["thumbNail"] = $this->_objectManager
                        ->create('\Magento\Catalog\Helper\Image')
                        ->init(
                            $_product,
                            "product_page_image_large"
                        )
                        ->keepFrame(true)
                        ->resize($width/3)
                        ->getUrl();

                    $customoptions = $this->_objectManager
                        ->create("\Magento\Catalog\Helper\Product\Configuration")
                        ->getOptions($item);
                    if (count($customoptions) > 0) {
                        $eachWishData["option"] = $customoptions;
                    }
                    $reviews = $this->_objectManager
                        ->create("\Magento\Review\Model\Review")
                        ->getResourceCollection()->addStoreFilter($storeId)
                        ->addEntityFilter(
                            "product",
                            $item->getProduct()->getId()
                        )->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED)
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
                    $eachWishData["rating"] = $rating;
                    $wishlistData[] = $eachWishData;
                }
                $returnArray["wishlistData"] = $wishlistData;

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
