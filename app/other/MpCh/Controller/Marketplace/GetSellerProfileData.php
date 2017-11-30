<?php
/**
 * Custom Software.
 *
 * @category  Custom
 *
 * @author    Custom
 * @copyright Copyright (c) 2010-2017 Custom Software Private Limited (https://Custom.com)
 * @license   https://store.Custom.com/license.html
 */
namespace Custom\MpChharo\Controller\Marketplace;

/**
 * MpChharo API .
 */
class GetSellerProfileData extends AbstractMarketplace
{
    /**
     * execute.
     *
     * @return string JSON
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $returnArray = [];
                $storeId = $this->getRequest()->getPost('storeId');
                $profileUrl = $this->getRequest()->getPost('profileUrl');
                $width = $this->getRequest()->getPost('width');
                /**
                 * $initialEnvironmentInfo store emulation start.
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $returnArray = [];
                $thisSeller = new \Magento\Framework\DataObject();
                $recentCollection = [];
                if ($profileUrl) {
                    $data = $this->_objectManager->create('\Custom\Marketplace\Model\SellerFactory')->create()->getCollection()->addFieldToFilter('shop_url', $profileUrl);
                    foreach ($data as $seller) {
                        $thisSeller = $seller;
                    }
                }
                $helper = $this->_marketplaceHelper;
                $sellerId = $thisSeller->getSellerId();
                $returnArray['sellerId'] = $sellerId;
                $seller = $this->_customerFactory->create()->load($sellerId);
                $shoptitle = $thisSeller->getShopTitle();
                if (!$shoptitle) {
                    $shoptitle = $seller->getName();
                }
                $returnArray['shopTitle'] = __('About %1', $shoptitle);
                $returnArray['sellerDescription'] = strip_tags($thisSeller->getCompanyDescription());
                // social data
                $returnArray['socialTitle'] = __('On the Social Web');
                $returnArray['IGIsActive'] = $thisSeller->getInstagramActive();
                $returnArray['IGId'] = $thisSeller->getInstagramId();
                $returnArray['PIIsActive'] = $thisSeller->getPinterestActive();
                $returnArray['PIId'] = $thisSeller->getPinterestId();
                $returnArray['FBIsActive'] = $thisSeller->getFbActive();
                $returnArray['FBId'] = $thisSeller->getFacebookid();
                $returnArray['TWIsActive'] = $thisSeller->getTwActive();
                $returnArray['TWId'] = $thisSeller->getTwitterid();
                $returnArray['GPIsActive'] = $thisSeller->getGplusActive();
                $returnArray['GPId'] = $thisSeller->getGplusId();
                $returnArray['VIIsActive'] = $thisSeller->getVimeoActive();
                $returnArray['VIId'] = $thisSeller->getVimeoId();
                $returnArray['YTIsActive'] = $thisSeller->getYoutubeActive();
                $returnArray['YTId'] = $thisSeller->getYoutubeId();
                $returnArray['MSIsActive'] = $thisSeller->getMoleskineActive();
                $returnArray['MSId'] = $thisSeller->getMoleskineId();
                // recent product collection
                $products = [];
                if ($sellerId) {
                    $data = $this->_objectManager->create('\Custom\Marketplace\Model\ProductFactory')->create()->getCollection()->addFieldToFilter('seller_id', $sellerId)->addFieldToFilter('status', ['neq' => 2]);
                    $data->getSelect()->group('mageproduct_id');
                    $data->setOrder('mageproduct_id', 'DESC');
                    $i = 0;
                    foreach ($data as $eachdata) {
                        $stockItemDetails = $this->_objectManager->create("\Magento\CatalogInventory\Api\StockRegistryInterface")
                        ->getStockItem($eachdata->getMageproductId());
                        $stockAvailability = $stockItemDetails->getIsInStock();

                        $product = $this->_objectManager->create("\Magento\Catalog\Model\ProductFactory")
                        ->create()
                        ->load($eachdata->getMageproductId());
                        if ($stockAvailability && $product->isVisibleInCatalog() && $product->isVisibleInSiteVisibility()) {
                            ++$i;
                            if ($i <= 6) {
                                array_push($products, $eachdata->getMageproductId());
                            }
                        }
                    }
                }
                foreach ($products as $productid) {
                    $_product = $this->_objectManager->create("\Magento\Catalog\Model\ProductFactory")
                    ->create()->load($productid);
                    $stockItem = $this->_objectManager->create("\Magento\CatalogInventory\Api\StockRegistryInterface")->getStockItem($productid);

                    if ($stockItem->getIsInStock() == 1 && $_product->getStatus() == 1) {
                        $recentCollection[] = $this->_helperCatalog->getOneProductRelevantData($_product, $storeId, $width);
                    }
                }
                $returnArray['recentCollection'] = $recentCollection;
                $returnArray['returnPolicy'] = html_entity_decode(strip_tags($thisSeller->getReturnpolicy()));
                $returnArray['shippingPolicy'] = html_entity_decode(strip_tags($thisSeller->getShippingpolicy()));
                // map data
                $locsearch = $thisSeller->getCompanyLocality() == '' ? $this->_objectManager
                ->create("\Magento\Directory\Model\CountryFactory")
                ->create()
                ->load(
                    $thisSeller->getCountryPic()
                )->getName() : $thisSeller->getCompanyLocality();
                $flagName = strtoupper($thisSeller->getCountryPic() == '' ? 'XX' : $thisSeller->getCountryPic()).'.png';
                $countryflag = $this->_viewFilePath.DS.'images'.DS.'country'.DS.'countryflags'.DS.$flagName;
                $countryName = '';
                if ($thisSeller->getCountryPic()) {
                    $countryModel = $this->_objectManager
                    ->create("\Magento\Directory\Model\CountryFactory")
                    ->create()
                    ->loadByCode($thisSeller->getCountryPic());
                    $countryName = ','.$countryModel->getName();
                }
                $returnArray['address'] = $locsearch.$countryName;

                $flagWidth = 140;
                $flagHeight = 90;
                $base_path =
                $this->_viewFilePath.DS.'images'.DS.'country'.DS.'countryflags'.DS.$flagName;

                $new_path = $this->_mediaDir.DS.'chharoresized'.DS.'marketplace'.DS.'images'.DS.'country'.DS.'countryflags'.DS.$flagWidth.'x'.$flagHeight.DS.$flagName;
                $new_url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'chharoresized'.DS.'marketplace'.DS.'images'.DS.'country'.DS.'countryflags'.DS.$flagWidth.'x'.$flagHeight.DS.$flagName;

                if (!file_exists($new_path)) {
                    $this->_helperCatalog->imageUpload(
                        $base_path,
                        $new_path,
                        $flagWidth,
                        $flagHeight
                    );
                }

                
                $returnArray['countryflagUrl'] = $new_url;
                $returnArray['profileUrl'] = $thisSeller->getShopUrl();

                $logowidth = $logoheight = $width / 2;
                $logoImage = $thisSeller->getLogoPic() == '' ? 'noimage.png' : $thisSeller->getLogoPic();
                $base_path = $this->_mediaDir.DS.'avatar'.DS.$logoImage;
                $new_path = $this->_mediaDir.DS.'chharoresized'.DS.'avatar'.DS.$logowidth.'x'.$logoheight.DS.$logoImage;

                $logoUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'chharoresized'.DS.'avatar'.DS.$logowidth.'x'.$logoheight.DS.$logoImage;

                if (!file_exists($new_path)) {
                    $this->_helperCatalog->imageUpload(
                        $base_path,
                        $new_path,
                        $logowidth,
                        $logoheight
                    );
                }

                $bannerWidth = $width;
                $bannerheight = $width / 2;
                $bannerImage = $thisSeller->getBannerPic() == '' ? 'noimage.png' : $thisSeller->getBannerPic();
                $base_path = $this->_mediaDir.DS.'avatar'.DS.$bannerImage;
                $new_path = $this->_mediaDir.DS.'chharoresized'.DS.'avatar'.DS.$bannerWidth.'x'.$bannerheight.DS.$bannerImage;

                $bannerUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'chharoresized'.DS.'avatar'.DS.$bannerWidth.'x'.$bannerheight.DS.$bannerImage;

                if (!file_exists($new_path)) {
                    $this->_helperCatalog->imageUpload(
                        $base_path,
                        $new_path,
                        $bannerWidth,
                        $bannerheight
                    );
                }
                $returnArray['bannerImage'] = $bannerUrl;
                $returnArray['logo'] = $logoUrl;
                $returnArray['sellerProductCount'] = $this->_marketplaceHelper->getSellerProCount($sellerId);
                $returnArray['shopRatingTitle'] = __("%1's Rating", $shoptitle);
                // rating data
                $feeds = $this->_marketplaceHelper->getFeedTotal($sellerId);

                if (!isset($feeds['feed_price'])) {
                    $feeds['feedprice'] = 0;
                }
                if (!isset($feeds['feed_value'])) {
                    $feeds['feedvalue'] = 0;
                }
                if (!isset($feeds['feed_quality'])) {
                    $feeds['feedquality'] = 0;
                }

                $returnArray['averageRatingData'][] = ['label' => __('Price'), 'value' => round(($feeds['price'] / 20), 1, PHP_ROUND_HALF_UP)];
                $returnArray['averageRatingData'][] = ['label' => __('Value'), 'value' => round(($feeds['value'] / 20), 1, PHP_ROUND_HALF_UP)];
                $returnArray['averageRatingData'][] = ['label' => __('Quality'), 'value' => round(($feeds['quality'] / 20), 1, PHP_ROUND_HALF_UP)];
                $returnArray['averageRatingCount'] = __('Reviwed').' '.__('%1 Times', $feeds['feedcount']);

                $feedCollection = $this->_objectManager
                ->create("\Custom\Marketplace\Model\FeedbackFactory")
                ->create()
                ->getCollection()
                ->addFieldToFilter('status', ['neq' => 0])
                ->addFieldToFilter('seller_id', $sellerId)
                ->setOrder('entity_id', 'DESC')
                ->setPageSize(2)
                ->setCurPage(1);

                $allRecentrating = [];

                foreach ($feedCollection as $keyed) {
                    $keyed = $keyed->getData();
                    $eachReview = [];
                    $feedCustomer = $this->_customerFactory->create()->load($keyed['userid']);
                    $name = $feedCustomer['firstname'].' '.$feedCustomer['lastname'];
                    $feedDatetime = strtotime($keyed['created_at']);
                    $feedDate = date('d-M-Y', $feedDatetime);
                    $eachReview['title'] = __('By %1', $name, $keyed['created_at']).' '.__('on').' '.__('%1', $feedDate);
                    $eachReview['summary'] = $keyed['feed_review'];
                    $eachReview['rating'][] = ['label' => __('Price'), 'value' => round(($keyed['feedprice'] / 20), 1, PHP_ROUND_HALF_UP)];
                    $eachReview['rating'][] = ['label' => __('Value'), 'value' => round(($keyed['feed_value'] / 20), 1, PHP_ROUND_HALF_UP)];
                    $eachReview['rating'][] = ['label' => __('Quality'), 'value' => round(($keyed['feed_quality'] / 20), 1, PHP_ROUND_HALF_UP)];
                    $allRecentrating[] = $eachReview;
                }
                $returnArray['recentRating'] = $allRecentrating;

                /*
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                $returnArray['success'] = 1;

                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $returnArray['success'] = 0;
                $returnArray['message'] = __($e->getMessage());

                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray['success'] = 0;
            $returnArray['message'] = __('Invalid Request.');

            return $this->getJsonResponse($returnArray);
        }
    }
}
