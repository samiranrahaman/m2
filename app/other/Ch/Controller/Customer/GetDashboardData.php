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
class GetDashboardData extends \Custom\Chharo\Controller\ApiController
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
        $this->_baseDir = $this->_objectManager->get("\Magento\Framework\Filesystem\DirectoryList")
            ->getPath('media').'/';
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
            $customerId = $this->getRequest()->getPost("customerId");
            $websiteId = $this->getRequest()->getPost("websiteId");
            $storeId = $this->getRequest()->getPost("storeId");

            if ($storeId == "") {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                /**
                 * $initialEnvironmentInfo store emulation start
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $customer = $this->_customerFactory->create()
                    ->setWebsiteId($websiteId)
                    ->loadByEmail($customerEmail);
                $customerId = $customer->getId();
                $returnArray = [];
                $returnArray["welcomeMsg"] = __("Hello")." ".$customer->getFirstname()." ".$customer->getLastname()."!";

                $orders = $this->_objectManager
                    ->create("\Magento\Sales\Model\ResourceModel\Order\CollectionFactory")
                    ->create()
                    ->addAttributeToSelect("*")
                    ->addAttributeToFilter(
                        "customer_id",
                        $customer->getId()
                    )
                    ->addAttributeToFilter(
                        "status",
                        [
                            "in" => $this->_objectManager
                                ->create("\Magento\Sales\Model\Order\Config")
                                ->getVisibleOnFrontStatuses()
                        ]
                    )
                    ->addAttributeToSort("created_at", "desc");
                    $orders->setPageSize(5)->setCurPage(1);
                $recentOrders = [];
                foreach ($orders as $key => $_order) {
                    $eachRecentOrder = [];
                    $eachRecentOrder["order_id"] = $_order->getRealOrderId();
                    $eachRecentOrder["date"] = $this->_helperCatalog->formatDate($_order->getCreatedAt());
                    $eachRecentOrder["ship_to"] = $_order->getShippingAddress() ? $this->_helperCatalog->stripTags(
                        $_order->getShippingAddress()->getName()
                    ) : " ";
                    $eachRecentOrder["order_total"] = $this->_helperCatalog
                        ->stripTags($_order->formatPrice($_order->getGrandTotal()));
                    $eachRecentOrder["status"] = $_order->getStatusLabel();
                    if ($this->canReorder($_order) == 1) {
                        $eachRecentOrder["canReorder"] = $this->canReorder($_order);
                    } else {
                        $eachRecentOrder["canReorder"] = 0;
                    }
                    $recentOrders[] = $eachRecentOrder;
                }
                $returnArray["recentOrders"] = $recentOrders;
                $returnArray["customerName"] = $customer->getFirstname()." ".$customer->getLastname();
                $returnArray["customerEmail"] = $customerEmail;
                $isSubscribed = $this->_objectManager
                    ->create("\Magento\Newsletter\Model\Subscriber")
                    ->loadByCustomerId($customer->getId())->isSubscribed();
                if ($isSubscribed) {
                    $returnArray["subscriptionMsg"] =
                    __("You are currently subscribed to 'General Subscription'.");
                } else {
                    $returnArray["subscriptionMsg"] =
                    __("You are currently not subscribed to any newsletter.");
                }
                $address = $customer->getPrimaryBillingAddress();
                if ($address instanceof \Magento\Framework\DataObject) {
                    $returnArray["billingAddress"] = $address->getFirstname()." ".$address->getLastname()."\n";
                    foreach ($address->getStreet() as $street) {
                        $returnArray["billingAddress"] .= $street."\n";
                    }
                    $returnArray["billingAddress"] .= $address->getCity().", ".$address->getRegion().", ".$address->getPostcode()."\n".$this->_objectManager->create("\Magento\Directory\Model\Country")->load($address->getCountryId())->getName()."\n"."T:".$address->getTelephone();
                    $returnArray["billingId"] = $address->getId();
                } else {
                    $returnArray["billingAddress"] =
                    __("You have not set a default billing address.");
                    $returnArray["billingId"] = "";
                }
                $address = $customer->getPrimaryShippingAddress();

                if ($address instanceof \Magento\Framework\DataObject) {
                    $returnArray["shippingAddress"] = $address->getFirstname()." ".$address->getLastname()."\n";
                    foreach ($address->getStreet() as $street) {
                        $returnArray["shippingAddress"] .= $street."\n";
                    }
                    $returnArray["shippingAddress"] .= $address->getCity().", ".$address->getRegion().", ".$address->getPostcode()."\n".$this->_objectManager->create("\Magento\Directory\Model\Country")->load($address->getCountryId())->getName()."\n"."T:".$address->getTelephone();
                    $returnArray["shippingId"] = $address->getId();
                } else {
                    $returnArray["shippingAddress"] =
                    __("You have not set a default shipping address.");
                    $returnArray["shippingId"] = "";
                }
                $reviewCollection = $this->_objectManager
                    ->create("\Magento\Review\Model\Review")
                    ->getProductCollection()
                    ->addStoreFilter($storeId)
                    ->addCustomerFilter($customer->getId())
                    ->setDateOrder()
                    ->setPageSize(5)
                    ->load()
                    ->addReviewSummary();
                $recentReviews = [];
                foreach ($reviewCollection as $key => $_review) {
                    $eachRecentReview = [];
                    $eachRecentReview["name"] = $this->_helperCatalog
                        ->stripTags($_review->getName());
                    if ($_review->getCount() > 0) {
                        $eachRecentReview["rating"] =
                        number_format((5*($_review->getSum() / $_review->getCount()))/100, 2, ".", "");
                    } else {
                        $eachRecentReview["rating"] = 0;
                    }
                    $recentReviews[] = $eachRecentReview;
                }
                $returnArray["recentReview"] = $recentReviews;
                $width = $this->getRequest()->getPost("width");
                if ($width != "") {
                    $width = $this->getRequest()->getPost("width");
                } else {
                    $width = 1000;
                }
                $height = $width/2;

                $collection = $this->_objectManager
                    ->create("\Custom\Chharo\Model\UserImage")
                    ->getCollection()
                    ->addFieldToFilter(
                        "customer_id",
                        $customer->getId()
                    );
                    $returnArray["customerBannerImage"] = "";
                    $returnArray["customerProfileImage"] = "";

                if ($collection->getSize() > 0) {
                    foreach ($collection as $value) {
                        if ($value->getBanner() != "") {
                            $basePath = $this->_baseDir.
                            'chharo'
                            .DS.
                            "customerpicture"
                            .DS.
                            $customerId
                            .DS.
                            $value->getBanner();

                            $newUrl = "";
                            if (file_exists($basePath)) {
                                $newPath = $this->_baseDir.
                                'chharo'
                                .DS.
                                "customerpicture"
                                .DS.
                                $customerId
                                .DS.
                                $width
                                ."x".
                                $height
                                .DS.
                                $value->getBanner();

                                $newUrl = $this->_helper->getUrl('media')
                                .'chharo'
                                .DS
                                ."customerpicture"
                                .DS.
                                $customerId
                                .DS.
                                $width
                                ."x".
                                $height
                                .DS.
                                $value->getBanner();

                                if (!file_exists($newPath)) {
                                    $uploadDir = $this->_baseDir
                                    .'chharo'
                                    .DS
                                    ."customerpicture"
                                    .DS.
                                    $customerId
                                    .DS.
                                    $width
                                    ."x".
                                    $height
                                    .DS;
                                    $removeFiles = glob($uploadDir.'*'); 
                                    foreach ($removeFiles as $rfile) { 
                                        if(is_file($rfile)) {
                                            unlink($rfile);
                                        } 
                                    }
                                    $this->_helperCatalog->imageUpload($basePath, $newPath, $width, $height);
                                }
                            }
                            $returnArray["customerBannerImage"] = $newUrl;
                        }

                        if ($value->getProfile() != "") {
                            $basePath = $this->_baseDir
                            .'chharo'
                            .DS
                            ."customerpicture"
                            .DS.
                            $customerId
                            .DS.
                            $value->getProfile();

                            $newUrl = "";
                            if (file_exists($basePath)) {
                                $newPath = $this->_baseDir
                                .'chharo'
                                .DS
                                ."customerpicture"
                                .DS.
                                $customerId
                                .DS.
                                "100x100"
                                .DS.
                                $value->getProfile();

                                $newUrl = $this->_helper->getUrl('media')
                                .'chharo'
                                .DS
                                ."customerpicture"
                                .DS.
                                $customerId
                                .DS.
                                "100x100"
                                .DS.
                                $value->getProfile();

                                if (!file_exists($newPath)) {
                                    $uploadDir = $this->_baseDir
                                    .'chharo'
                                    .DS
                                    ."customerpicture"
                                    .DS.
                                    $customerId
                                    .DS.
                                    "100x100"
                                    .DS;
                                    $removeFiles = glob($uploadDir.'*'); 
                                    foreach ($removeFiles as $rfile) { 
                                        if(is_file($rfile)) {
                                            unlink($rfile);
                                        } 
                                    }
                                    $this->_helperCatalog->imageUpload($basePath, $newPath, 100, 100);
                                }
                            }
                            $returnArray["customerProfileImage"] = $newUrl;
                        }
                    }
                }

                /**
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                $returnArray["success"] = 1;
                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray["success"] = 0;
                $returnArray["message"] = __("Invalid request");
                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray["success"] = 0;
            $returnArray["message"] = __("Invalid Request.");
            return $this->getJsonResponse($returnArray);
        }
    }
}
