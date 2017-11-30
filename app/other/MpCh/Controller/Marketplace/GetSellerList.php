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
class GetSellerList extends AbstractMarketplace
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
                
                $width = $this->getRequest()->getPost("width");
                $shopTitle = $this->getRequest()->getPost("shopTitle");
                /**
                 * $initialEnvironmentInfo store emulation start
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);
                
                $height = $width/2;
                $sellerArray = [];
                $returnArray = [];
                $allSellers = [];
                $sellerProductCollection = $this->_objectManager->create("\Custom\Marketplace\Model\ProductFactory")
                ->create()
                ->getCollection()
                ->addFieldToFilter("status", 1)
                ->addFieldToSelect("seller_id")
                ->distinct(true);

                foreach ($sellerProductCollection as $value) {
                    array_push($sellerArray, $value["seller_id"]);
                }
                $collection = $this->_objectManager->create("\Custom\Marketplace\Model\SellerFactory")->create()->getCollection()
                ->addFieldToFilter("is_seller", 1)
                ->addFieldToFilter("seller_id", ["in" => $sellerArray]);

                if ($shopTitle != "") {
                    $collection->addFieldToFilter(
                        "shop_title",
                        ["like" => "%".$shopTitle."%"]
                    );
                }
                $collection->setOrder("entity_id", "DESC");
                
                $helper = $this->_marketplaceHelper;
                $bannerDisplay = $helper->getDisplayBanner();
                $bannerImage = $helper->getBannerImage();
                $bannerContent = $helper->getBannerContent();
                $marketplaceButton = $helper->getMarketplacebutton();
                $sellerlistTopLabel = $helper->getSellerlisttopLabel();
                $bannerImagePath = explode(DS, $bannerImage);
                if ($bannerDisplay && end($bannerImagePath)) {
                    
                    $base_path = $this->_helperCatalog->getBasePath("media").DS."marketplace".DS."banner".DS.end($bannerImagePath);
                    $new_path = $this->_helperCatalog->getBasePath("media").DS."chharoresized".DS."marketplace".DS."default".DS.$width."x".$height.DS.end($bannerImagePath);
                    $new_url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)."chharoresized".DS."marketplace".DS."default".DS.$width."x".$height.DS.end($bannerImagePath);
                    if (!file_exists($new_path)) {
                        $this->_helperCatalog->imageUpload(
                            $base_path,
                            $new_path,
                            $width,
                            $height
                        );
                    }
                    $returnArray["bannerImage"] = $new_url;
                    $returnArray["banner"][] = $marketplaceButton;
                    $returnArray["banner"][] = $this->_helperCatalog->stripTags($bannerContent);
                }
                $returnArray["topLabel"] = $sellerlistTopLabel;
                $logowidth = $logoheight = $width/4;
                foreach ($collection as $seller_coll) {
                    $eachSeller = [];
                    $seller_id = $seller_coll->getseller_id();
                    $seller = $this->_customerFactory->create()->load($seller_id);
                    $seller_product_count = 0;
                    $profileurl = $seller_coll->getShopUrl();
                    $shoptitle = "";
                    $logoImage = "noimage.png";
                    $seller_product_count = $helper->getSellerProCount($seller_id);
                    $shoptitle = $seller_coll->getShopTitle();
                    $logoImage = $seller_coll->getLogoPic() == "" ? "noimage.png" : $seller_coll->getLogoPic();
                    if (!$shoptitle) {
                        $shoptitle = $seller->getName();
                    }
                    $base_path = $this->_helperCatalog->getBasePath("media").DS."avatar".DS.$logoImage;
                    $new_path = $this->_helperCatalog->getBasePath("media").DS."chharoresized".DS."avatar".DS.$logowidth."x".$logoheight.DS.$logoImage;
                    $logoUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)."chharoresized".DS."avatar".DS.$logowidth."x".$logoheight.DS.$logoImage;
                    if (!file_exists($new_path)) {
                        $this->_helperCatalog->imageUpload(
                            $base_path,
                            $new_path,
                            $logowidth,
                            $logoheight
                        );
                    }
                    $eachSeller["shopTitle"] = $shoptitle;
                    $eachSeller["profileurl"] = $profileurl;
                    $eachSeller["sellerIcon"] = $logoUrl;
                    $eachSeller["sellerProductCount"] = $seller_product_count;
                    $allSellers[] = $eachSeller;
                }
                $returnArray["sellers"] = $allSellers;

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
