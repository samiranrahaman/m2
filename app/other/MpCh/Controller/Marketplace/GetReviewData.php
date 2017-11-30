<?php
/**
 * Custom Software.
 *
 * @category  Custom
 * @package   Custom_MpChharo
 * @author    Custom
 * @copyright Copyright (c) 2010-2017 Custom Software Private Limited (https://Custom.com)
 * @license   https://store.Custom.com/license.html
 */

namespace Custom\MpChharo\Controller\Marketplace;

/**
 * MpChharo API .
 */
class GetReviewData extends AbstractMarketplace
{

    /**
     * execute
     * @return string JSON
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $returnArray = [];
                $storeId = $this->getRequest()->getPost("storeId");
                $profileUrl = $this->getRequest()->getPost("profileUrl");
                $width = $this->getRequest()->getPost("width");
                $sessionId = $this->getRequest()->getPost("sessionId");
                $customerId = $this->getRequest()->getPost("customerId");

                /**
                 * $initialEnvironmentInfo store emulation start
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $thisSeller = new \Magento\Framework\DataObject();
                $recentCollection = [];

                if ($profileUrl) {
                    $data = $this->_objectManager->create("Custom\Marketplace\Model\SellerFactory")->create()
                    ->getCollection()
                    ->addFieldToFilter("shop_url", $profileUrl);
                    foreach ($data as $seller) {
                        $thisSeller = $seller;
                    }
                }
                $helper = $this->_marketplaceHelper;
                $sellerId = $thisSeller->getSellerId();
                $flag = 2;
                $feedavailflag = 0;
                $ordercount = 0;
                $feedbackcount = 0;

                if ($this->_marketplaceHelper->getReviewStatus()) {
                    $flag = 1;
                    $collectionfeed =
                    $this->_objectManager->create("Custom\Marketplace\Model\FeedbackcountFactory")->create()
                    ->getCollection()
                    ->addFieldToFilter("buyer_id", $customerId)
                    ->addFieldToFilter("seller_id", $sellerId);
                    foreach ($collectionfeed as $value) {
                        $ordercount = $value->getOrderCount();
                        $feedbackcount = $value->getFeedbackCount();
                    }

                    if ($feedbackcount < $ordercount) {
                        $feedavailflag = 1;
                    }
                }

                if (($flag == 2) || ($flag == 1 && $feedavailflag == 1)) {
                    $returnArray["canReview"] = 1;
                } else {
                    $returnArray["canReview"] = 0;
                    $returnArray["canReviewMsg"] = __("You need to purchase item(s) first to make a review.");
                }
                $seller = $this->_customerFactory->create()->load($sellerId);
                $returnArray["sellerId"] = $sellerId;
                $shoptitle = $thisSeller->getShopTitle();
                if (!$shoptitle) {
                    $shoptitle = $seller->getName();
                }
                $returnArray["shopTitle"] = $shoptitle;
                // map data
                $locsearch = $thisSeller->getCompanyLocality() == "" ? $this->_objectManager
                ->create("\Magento\Directory\Model\CountryFactory")
                ->create()
                ->load($thisSeller->getCountryPic())->getName() : $thisSeller->getCompanyLocality();

                $flagName = strtoupper($thisSeller->getCountryPic() == "" ? "XX" : $thisSeller->getCountryPic()).".png";

                $countryflag = $this->_viewFilePath.DS."images".DS."country".DS."countryflags".DS.$flagName;
                $countryName = "";
                if ($thisSeller->getCountryPic()) {
                    $countryModel = $this->_objectManager
                    ->create("\Magento\Directory\Model\CountryFactory")
                    ->create()
                    ->loadByCode($thisSeller->getCountryPic());
                    $countryName = ",".$countryModel->getName();
                }

                $returnArray["address"] = $locsearch.$countryName;
                $flagWidth = 140;
                $flagHeight = 90;
                $this->_viewFilePath.DS."images".DS."country".DS."countryflags".DS.$flagName;

                $new_path = $this->_mediaDir.DS."chharoresized".DS."marketplace".DS."images".DS."country".DS."countryflags".DS.$flagWidth."x".$flagHeight.DS.$flagName;
                $new_url =$this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)."chharoresized".DS."marketplace".DS."images".DS."country".DS."countryflags".DS.$flagWidth."x".$flagHeight.DS.$flagName;

                if (!file_exists($new_path)) {
                    $this->_helperCatalog->imageUpload(
                        $base_path,
                        $new_path,
                        $flagWidth,
                        $flagHeight
                    );
                }
                $returnArray["countryflagUrl"] = $new_url;

                $returnArray["profileUrl"] = $thisSeller->getShopUrl();

                $logowidth = $logoheight = $width/2;

                $logoImage = $thisSeller->getlogopic() == "" ? "noimage.png" : $thisSeller->getlogopic();

                $base_path = $this->_mediaDir.DS."avatar".DS.$logoImage;

                $base_path = $this->_mediaDir.DS."avatar".DS.$logoImage;
                $new_path = $this->_mediaDir.DS."chharoresized".DS."avatar".DS.$logowidth."x".$logoheight.DS.$logoImage;

                $logoUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)."chharoresized".DS."avatar".DS.$logowidth."x".$logoheight.DS.$logoImage;

                if (!file_exists($new_path)) {
                     $this->_helperCatalog->imageUpload(
                         $base_path,
                         $new_path,
                         $logowidth,
                         $logoheight
                     );
                }
                $returnArray["logo"] = $logoUrl;

                $returnArray["sellerProductCount"] = $helper->getSellerProCount($sellerId);

                $reviewCollection = $this->_objectManager
                ->create("\Custom\Marketplace\Model\FeedbackFactory")
                ->create()
                ->getCollection()
                ->addFieldToFilter("status", ["neq" => 0])
                ->addFieldToFilter("seller_id", $sellerId);

                $allReviews = [];

                foreach ($reviewCollection as $oneReview) {
                    $oneReview = $oneReview->getData();
                    $eachReview = [];
                    $eachReview["rating"][] = ["label" => __("Price"), "value" => round(($oneReview["feed_price"]/20), 1, PHP_ROUND_HALF_UP)];
                    $eachReview["rating"][] = ["label" => __("Value"), "value" => round(($oneReview["feed_value"]/20), 1, PHP_ROUND_HALF_UP)];
                    $eachReview["rating"][] = ["label" => __("Quality"), "value" => round(($oneReview["feed_quality"]/20), 1, PHP_ROUND_HALF_UP)];
                    $eachReview["summary"] = $oneReview["feed_review"];
                    $reviewCustomer = $this->_customerFactory->create()->load($oneReview["seller_id"]);
                    $name = $reviewCustomer["firstname"]." ".$reviewCustomer["lastname"];
                    $reviewDatetime = strtotime($oneReview["created_at"]);
                    $reviewDate = date("d-M-Y", $reviewDatetime);
                    $eachReview["title"] = __("By %1", $name, $oneReview["created_at"])." ".__("on")." ".__("%1", $reviewDate);
                    $allReviews[] = $eachReview;
                }
                $returnArray["allReviews"] = $allReviews;

                /**
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
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
