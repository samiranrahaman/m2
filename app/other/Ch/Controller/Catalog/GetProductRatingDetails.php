<?php
/**
 * Custom Software.
 *
 * @category Custom
 *
 * @author    Custom
 * @copyright Copyright (c) 2010-2017 Custom Software Private Limited (https://Custom.com)
 * @license   https://store.Custom.com/license.html
 */

namespace Custom\Chharo\Controller\Catalog;

use Magento\Framework\App\Action\Context;
use Custom\Chharo\Helper\Data as HelperData;
use Custom\Chharo\Helper\Catalog as HelperCatalog;
use Magento\Store\Model\App\Emulation;

/**
 * Chharo API Catalog controller.
 */
class GetProductRatingDetails extends \Custom\Chharo\Controller\ApiController
{
    /**
     * $_catalogImage.
     *
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_catalogImage;

    protected $_priceFormat;
    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        HelperData $helper,
        HelperCatalog $helperCatalog,
        Emulation $emulate,
        \Magento\Catalog\Helper\Image $catalogImage
    ) {
        $this->_catalogImage = $catalogImage;
        parent::__construct($context, $helper, $helperCatalog, $emulate);

        $this->_priceFormat = $this->_objectManager
            ->create('Magento\Framework\Pricing\Helper\Data');
    }

    /**
     * execute category list.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $returnArray = [];
            $allReviews = [];
            $ratingArray = [];
            $storeId = $this->getRequest()->getPost('storeId');
            $productId = $this->getRequest()->getPost('productId');
            if ($storeId == '') {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                /**
                 * $initialEnvironmentInfo store emulation start.
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                // Getting Produc Rating Details
                $_product = $this->_objectManager
                    ->create("\Magento\Catalog\Model\Product")
                    ->load($productId);

                $returnArray['name'] = $_product->getName();

                $returnArray['thumbNail'] = $this->_helperCatalog->getImageUrl($_product, 200, 'product_page_image_small');

                $returnArray['formatedFinalPrice'] = $this->_helperCatalog
                    ->stripTags($this->_priceFormat->currency($_product->getFinalPrice()));
                $returnArray['finalPrice'] = $_product->getFinalPrice();
                $returnArray['formatedMinPrice'] = $this->_helperCatalog
                    ->stripTags(
                        $this->_priceFormat->currency(
                            $_product->getMinPrice()
                        )
                    );
                $returnArray['minPrice'] = $_product->getMinPrice();
                $returnArray['formatedMaxPrice'] = $this->_helperCatalog
                    ->stripTags(
                        $this->_priceFormat->currency(
                            $_product->getMaxPrice()
                        )
                    );
                $returnArray['maxPrice'] = $_product->getMaxPrice();
                $returnArray['formatedSpecialPrice'] = $this->_helperCatalog
                    ->stripTags(
                        $this->_priceFormat->currency(
                            $_product->getSpecialPrice()
                        )
                    );
                $returnArray['specialPrice'] = $_product->getSpecialPrice();
                $returnArray['typeId'] = $_product->getTypeId();
                $ratingCollection = $this->_objectManager
                    ->create('\Magento\Review\Model\Rating')
                    ->getResourceCollection()
                    ->addEntityFilter('product')
                    ->setPositionOrder()
                    ->setStoreFilter($storeId)
                    ->addRatingPerStoreName($storeId)
                    ->load();
                $ratingCollection->addEntitySummaryToItem($productId, $storeId);
                foreach ($ratingCollection as $_rating) {
                    if ($_rating->getSummary()) {
                        $eachRating = [];
                        $eachRating['ratingCode'] = $this->_helperCatalog->stripTags($_rating->getRatingCode());
                        $eachRating['ratingValue'] = number_format((5 * $_rating->getSummary()) / 100, 2, '.', '');
                        $ratingArray[] = $eachRating;
                    }
                }
                $returnArray['ratingData'] = $ratingArray;
                $reviewCollection = $this->_objectManager
                    ->create('\Magento\Review\Model\Review')
                    ->getResourceCollection()->addStoreFilter($storeId)
                    ->addEntityFilter(
                        'product',
                        $productId
                    )->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED)
                    ->setDateOrder()->addRateVotes();

                foreach ($reviewCollection as $_review) {
                    $oneReview = [];
                    $ratings = [];
                    $oneReview['title'] = $this->_helperCatalog->stripTags($_review->getTitle());
                    $oneReview['details'] = $this->_helperCatalog->stripTags($_review->getDetail());
                    $_votes = $_review->getRatingVotes();
                    if (count($_votes)) {
                        foreach ($_votes as $_vote) {
                            $oneVote = [];
                            $oneVote['label'] = $this->_helperCatalog->stripTags($_vote->getRatingCode());
                            $oneVote['value'] = number_format($_vote->getValue(), 2, '.', '');
                            $ratings[] = $oneVote;
                        }
                    }
                    $oneReview['ratings'] = $ratings;
                    $oneReview['reviewBy'] = __(
                        'Review by %1',
                        $this->_helperCatalog->stripTags(
                            $_review->getNickname()
                        )
                    );
                    $oneReview['reviewOn'] = __(
                        '(Posted on %1)',
                        $this->_helperCatalog->formatDate(
                            $_review->getCreatedAt()
                        ),
                        'long'
                    );
                    $allReviews[] = $oneReview;
                }
                $returnArray['reviewList'] = $allReviews;

                $ratingCollection = $this->_objectManager
                    ->create('\Magento\Review\Model\Rating')
                    ->getResourceCollection()
                    ->addEntityFilter('product')
                    ->setPositionOrder()
                    ->addRatingPerStoreName($storeId)
                    ->setStoreFilter($storeId)
                    ->load()
                    ->addOptionToItems();
                $allRatingFormData = [];
                foreach ($ratingCollection as $_rating) {
                    $eachTypeRating = [];
                    $ratingFormData = [];
                    foreach ($_rating->getOptions() as $_option) {
                        $eachTypeRating[] = $_option->getId();
                    }
                    $ratingFormData['id'] = $_rating->getId();
                    $ratingFormData['name'] = $this->_helperCatalog
                        ->stripTags($_rating->getRatingCode());
                    $ratingFormData['values'] = $eachTypeRating;
                    $allRatingFormData[] = $ratingFormData;
                }
                $returnArray['ratingFormData'] = $allRatingFormData;

                /*
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                $returnArray['success'] = 1;

                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray['success'] = 0;
                $returnArray['message'] = __('Invalid Request.');

                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray['success'] = 0;
            $returnArray['message'] = __('Invalid Request.');

            return $this->getJsonResponse($returnArray);
        }
    }

    private function getCategoryList()
    {
        return [];
    }
}
