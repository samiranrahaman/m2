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

//define("DS", DIRECTORY_SEPARATOR);
/**
 * MpChharo API .
 */
class GetLandingPageData extends AbstractMarketplace
{
    /**
     * execute get landing page data.
     *
     * @return string JSON
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $storeId = $this->getRequest()->getPost('storeId');
                $width = $this->getRequest()->getPost('width');
                $returnArray = [];
                /**
                 * $initialEnvironmentInfo store emulation start.
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $height = $width / 2;
                $returnArray = [];
                $allSellers = [];
                $helper = $this->_marketplaceHelper;
                $marketplacelabel1 = $helper->getMarketplacelabel1();
                $marketplacelabel2 = $helper->getMarketplacelabel2();
                $marketplacelabel3 = $helper->getMarketplacelabel3();
                $marketplacelabel4 = $helper->getMarketplacelabel4();
                $bannerDisplay = $helper->getDisplayBanner();
                $bannerImage = $helper->getBannerImage();
                $bannerContent = $helper->getBannerContent();
                $iconsDisplay = $helper->getDisplayIcon();
                $iconImage1 = $helper->getIconImage1();
                $iconImage1Label = $helper->getIconImageLabel1();
                $iconImage2 = $helper->getIconImage2();
                $iconImage2Label = $helper->getIconImageLabel2();
                $iconImage3 = $helper->getIconImage3();
                $iconImage3Label = $helper->getIconImageLabel3();
                $iconImage4 = $helper->getIconImage4();
                $iconImage4Label = $helper->getIconImageLabel4();
                $marketplacebutton = $helper->getMarketplacebutton();
                $marketplaceprofile = $helper->getMarketplaceprofile();
                /*order collection*/
                $sellers_order = $this->_objectManager->create("Custom\Marketplace\Model\OrdersFactory")
                ->create()
                ->getCollection()
                ->addFieldToFilter(
                    'invoice_id',
                    ['neq' => 0]
                )
                ->addFieldToSelect('seller_id');

                $sellers_order->getSelect()
                ->join(
                    ['ccp' => $this->_resourceConnection->getTableName('marketplace_userdata')],
                    'ccp.seller_id = main_table.seller_id',
                    ['is_seller' => 'is_seller']
                )
                ->where('ccp.is_seller = 1');

                $sellers_order->getSelect()
                ->columns('COUNT(*) as countOrder')->group('seller_id');
                $seller_arr = [];
                foreach ($sellers_order as $value) {
                    if ($helper->getSellerProCount($value['seller_id'])) {
                        $seller_arr[$value['seller_id']] = [];
                        $seller_products = $this->_objectManager->create("Custom\Marketplace\Model\SaleslistFactory")
                        ->create()->getCollection()
                                        ->addFieldToFilter('main_table.seller_id', $value['seller_id'])
                                        ->addFieldToFilter('cpprostatus', 1)
                                        ->addFieldToSelect('mageproduct_id')
                                        ->addFieldToSelect('magequantity');
                        $seller_products->getSelect()
                                        ->columns('SUM(magequantity) as countOrderedProduct')
                                        ->group('main_table.mageproduct_id');
                        $seller_products->getSelect()
                                        ->joinLeft(
                                            ['ccp' => $this->_resourceConnection->getTableName('marketplace_product')],
                                            'ccp.mageproduct_id = main_table.mageproduct_id',
                                            ['status' => 'status']
                                        )->where('ccp.status = 1');

                        $seller_products->setOrder('countOrderedProduct', 'DESC')->setPageSize(3);
                        // echo $seller_products->getSelect();
                        // die;
                        foreach ($seller_products as $seller_product) {
                            array_push($seller_arr[$value['seller_id']], $seller_product['mageproduct_id']);
                        }
                    }
                }
                if (count($seller_arr) != 4) {
                    $i = count($seller_arr);
                    $count_pro_arr = [];
                    $seller_product_coll = $this->_objectManager->create("Custom\Marketplace\Model\ProductFactory")
                        ->create()->getCollection()->addFieldToFilter('status', 1);
                    $seller_product_coll
                    ->getSelect()->join(
                        ['ccp' => $this->_resourceConnection->getTableName('marketplace_userdata')],
                        'ccp.seller_id = main_table.seller_id',
                        ['is_seller' => 'is_seller']
                    )
                    ->where('ccp.is_seller = 1');

                    $seller_product_coll->getSelect()->columns('COUNT(*) as countOrder')->group('main_table.seller_id');

                    foreach ($seller_product_coll as $value) {
                        if (!isset($count_pro_arr[$value['seller_id']])) {
                            $count_pro_arr[$value['seller_id']] = [];
                        }
                        $count_pro_arr[$value['seller_id']] = $value['countOrder'];
                    }
                    arsort($count_pro_arr);
                    foreach ($count_pro_arr as $procount_seller_id => $procount) {
                        if ($i <= 4) {
                            if ($helper->getSellerProCount($procount_seller_id)) {
                                if (!isset($seller_arr[$procount_seller_id])) {
                                    $seller_arr[$procount_seller_id] = [];
                                }
                                $seller_product_coll = $this->_objectManager->create("Custom\Marketplace\Model\ProductFactory")
                                    ->create()->getCollection()
                                    ->addFieldToFilter('seller_id', $procount_seller_id)
                                    ->addFieldToFilter('status', 1)
                                    ->setPageSize(3);
                                foreach ($seller_product_coll as $value) {
                                    array_push($seller_arr[$procount_seller_id], $value['mageproduct_id']);
                                }
                            }
                        }
                        ++$i;
                    }
                }
                if ($bannerDisplay) {
                    $bannerImagePath = explode(DS, $bannerImage);

                    if (0) {
                        $base_path = $this->_mediaDir.DS.'marketplace'.DS.'banner'.DS.end($bannerImagePath);
                        $new_path = $this->_mediaDir.DS.'chharoresized'.DS.'marketplace'.DS.'banner'.DS.$width.'x'.$height.DS.end($bannerImagePath);
                        $new_url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'chharoresized'.DS.'marketplace'.DS.'banner'.DS.$width.'x'.$height.DS.end($bannerImagePath);
                        if (!file_exists($new_path)) {
                            $this->_helperCatalog->imageUpload(
                                $base_path,
                                $new_path,
                                $width,
                                $height
                            );
                        }
                        $returnArray['bannerImage'] = $new_url;
                    }
                    $returnArray['banner'][] = $marketplacebutton;
                    $returnArray['banner'][] = strip_tags($bannerContent);
                }
                $returnArray['label1'] = $marketplacelabel1;
                if ($iconsDisplay) {
                    $returnArray['icons'][] = ['image' => $iconImage1, 'label' => $iconImage1Label];
                    $returnArray['icons'][] = ['image' => $iconImage2, 'label' => $iconImage2Label];
                    $returnArray['icons'][] = ['image' => $iconImage3, 'label' => $iconImage3Label];
                    $returnArray['icons'][] = ['image' => $iconImage4, 'label' => $iconImage4Label];
                }
                $returnArray['label2'] = $marketplacelabel2;
                $i = 0;
                $count = count($seller_arr);
                $logowidth = $logoheight = $width / 4;
                foreach ($seller_arr as $seller_id => $products) {
                    $eachSeller = [];
                    ++$i;
                    $seller = $this->_customerFactory->create()->load($seller_id);
                    $seller_product_count = 0;
                    $profileurl = 0;
                    $shoptitle = '';
                    $logoImage = 'noimage.png';
                    $seller_product_count = $helper->getSellerProCount($seller_id);
                    $seller_data = $this->_objectManager->create("\Custom\Marketplace\Model\SellerFactory")
                        ->create()->getCollection()->addFieldToFilter('seller_id', $seller_id);
                    foreach ($seller_data as $seller_data_result) {
                        $profileurl = $seller_data_result->getShopUrl();
                        $shoptitle = $seller_data_result->getShopTitle();
                        $logoImage = $seller_data_result->getlogoPic() == '' ? 'noimage.png' : $seller_data_result->getLogoPic();
                    }
                    if (!$shoptitle) {
                        $shoptitle = $seller->getName();
                    }
                    $base_path = $this->_mediaDir.DS.'avatar'.DS.$logoImage;
                    $new_path = $this->_mediaDir.DS.'chharoresized'.DS.'avatar'.DS.$logowidth.'x'.$logoheight.DS.$logoImage;
                    $logoUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'chharoresized'.DS.'avatar'.DS.$logowidth.'x'.$logoheight.DS.$logoImage;
                    if (file_exists($base_path)) {
                        if (!file_exists($new_path)) {
                            $this->_helperCatalog->imageUpload(
                                $base_path,
                                $new_path,
                                $logowidth,
                                $logoheight
                            );
                        }
                    }
                    if (!isset($products[0])) {
                        $products[0] = 0;
                        $seller_product_row = $this->_objectManager->create("Custom\Marketplace\Model\ProductFactory")
                                    ->create()->getCollection()
                                        ->addFieldToFilter('seller_id', $seller_id)
                                        ->addFieldToFilter('status', 1);
                        if (isset($products[1])) {
                            $seller_product_row->addFieldToFilter('mageproduct_id', ['neq' => $products[1]]);
                        }
                        if (isset($products[2])) {
                            $seller_product_row->addFieldToFilter('mageproduct_id', ['neq' => $products[2]]);
                        }
                        $seller_product_row->getSelect()
                                        ->columns('COUNT(*) as countproducts')
                                        ->group('seller_id');
                        foreach ($seller_product_row as $seller_product_row_data) {
                            $products[0] = $seller_product_row_data['mageproduct_id'];
                        }
                    }
                    if (!isset($products[1])) {
                        $products[1] = 0;
                        $seller_product_row = $this->_objectManager->create("Custom\Marketplace\Model\ProductFactory")
                                    ->create()->getCollection()
                                        ->addFieldToFilter('seller_id', $seller_id)
                                        ->addFieldToFilter('status', 1);
                        if (isset($products[0])) {
                            $seller_product_row->addFieldToFilter('mageproduct_id', ['neq' => $products[0]]);
                        }
                        if (isset($products[2])) {
                            $seller_product_row->addFieldToFilter('mageproduct_id', ['neq' => $products[2]]);
                        }
                        $seller_product_row->getSelect()
                                        ->columns('COUNT(*) as countproducts')
                                        ->group('seller_id');
                        foreach ($seller_product_row as $seller_product_row_data) {
                            $products[1] = $seller_product_row_data['mageproduct_id'];
                        }
                    }
                    if (!isset($products[2])) {
                        $products[2] = 0;
                        $seller_product_row = $this->_objectManager->create("Custom\Marketplace\Model\ProductFactory")
                                    ->create()->getCollection()
                                        ->addFieldToFilter('seller_id', $seller_id)
                                        ->addFieldToFilter('status', 1);
                        if (isset($products[1])) {
                            $seller_product_row->addFieldToFilter('mageproduct_id', ['neq' => $products[1]]);
                        }
                        if (isset($products[0])) {
                            $seller_product_row->addFieldToFilter('mageproduct_id', ['neq' => $products[0]]);
                        }
                        $seller_product_row->getSelect()
                                        ->columns('COUNT(*) as countproducts')
                                        ->group('seller_id');
                        foreach ($seller_product_row as $seller_product_row_data) {
                            $products[2] = $seller_product_row_data['mageproduct_id'];
                        }
                    }
                    $product_1 = $this->_objectManager->create("Magento\Catalog\Model\ProductFactory")
                                    ->create()->load($products[0]);
                    $product_2 = $this->_objectManager->create("Magento\Catalog\Model\ProductFactory")
                                    ->create()->load($products[1]);
                    $product_3 = $this->_objectManager->create("Magento\Catalog\Model\ProductFactory")
                                    ->create()->load($products[2]);

                    $eachSeller['pro1id'] = $product_1->getid();
                    $eachSeller['pro1name'] = $product_1->getName();
                    $eachSeller['pro1type'] = $product_1->getTypeId();

                    $eachSeller['pro1thumbnail'] = $this->_helperCatalog->getImageUrl(
                        $product_1,
                        $width / 2.5,
                        'product_page_image_large'
                    );
                    $eachSeller['pro2id'] = $product_2->getid();
                    $eachSeller['pro2name'] = $product_2->getName();
                    $eachSeller['pro2type'] = $product_2->getTypeId();
                    $eachSeller['pro2thumbnail'] =
                    $this->_helperCatalog->getImageUrl(
                        $product_2,
                        $width / 2.5,
                        'product_page_image_large'
                    );

                    $eachSeller['pro3id'] = $product_3->getid();
                    $eachSeller['pro3name'] = $product_3->getName();
                    $eachSeller['pro3type'] = $product_3->getTypeId();
                    $eachSeller['pro3thumbnail'] =
                    $this->_helperCatalog->getImageUrl(
                        $product_3,
                        $width / 2.5,
                        'product_page_image_large'
                    );
                    $eachSeller['shopTitle'] = $shoptitle;
                    $eachSeller['profileurl'] = $profileurl;
                    $eachSeller['sellerIcon'] = $logoUrl;
                    $eachSeller['sellerProductCount'] = $seller_product_count;
                    $allSellers[] = $eachSeller;
                }
                $returnArray['sellers'] = $allSellers;
                $returnArray['label3'] = $marketplacelabel3;
                $returnArray['label4'] = $marketplacelabel4;
                $returnArray['aboutImage'] = strip_tags($marketplaceprofile);

                /*
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);

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
