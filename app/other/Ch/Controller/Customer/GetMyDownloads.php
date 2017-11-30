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
class GetMyDownloads extends \Custom\Chharo\Controller\ApiController
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

           
            try {
                $customer = $this->_customerFactory
                    ->create()
                    ->setWebsiteId($websiteId)
                    ->loadByEmail($customerEmail);
                $returnArray = [];

                $purchased = $this->_objectManager
                    ->create("\Magento\Downloadable\Model\ResourceModel\Link\Purchased\Collection")
                    ->addFieldToFilter("customer_id", $customer->getId())
                    ->addOrder("created_at", "desc");

                $purchasedIds = [];
                foreach ($purchased as $_item) {
                    $purchasedIds[] = $_item->getId();
                }
                if (empty($purchasedIds)) {
                    $purchasedIds = [null];
                }
                $purchasedItems = $this->_objectManager
                    ->create("\Magento\Downloadable\Model\ResourceModel\Link\Purchased\Item\Collection")
                    ->addFieldToFilter("purchased_id", ["in" => $purchasedIds])
                    ->addFieldToFilter(
                        "status",
                        [
                        "nin" => [
                            \Magento\Downloadable\Model\Link\Purchased\Item::LINK_STATUS_PENDING_PAYMENT,
                            \Magento\Downloadable\Model\Link\Purchased\Item::LINK_STATUS_PAYMENT_REVIEW
                        ]
                        ]
                    )
                    ->setOrder("item_id", "desc");

                $pageNumber = $this->getRequest()->getPost("pageNumber");

                if ($pageNumber != "") {
                    $pageNumber = $this->getRequest()->getPost("pageNumber");
                    $returnArray["totalCount"] = $purchasedItems->getSize();
                    $purchasedItems->setPageSize(16)->setCurPage($pageNumber);
                }
                foreach ($purchasedItems as $item) {
                    $item->setPurchased($purchased->getItemById($item->getPurchasedId()));
                }
                $allDownloads = [];
                foreach ($purchasedItems as $key => $downloads) {
                    $eachDownloads = [];
                    $eachDownloads["incrementId"] = $incrementId = $downloads->getPurchased()->getOrderIncrementId();
                    $_order = $this->_objectManager
                        ->create("\Magento\Sales\Model\Order")
                        ->loadByIncrementId($incrementId);
                    if ($_order->getRealOrderId() > 0) {
                        $eachDownloads["isOrderExist"] = 1;
                    } else {
                        $eachDownloads["isOrderExist"] = 0;
                        $eachDownloads["message"] = __("Sorry This Order Does not Exist!!");
                    }
                    $eachDownloads["hash"] = $downloads->getLinkHash();
                    $eachDownloads["date"] = $this->_helperCatalog->formatDate(
                        $downloads->getPurchased()->getCreatedAt()
                    );
                    $eachDownloads["proName"] =$this->_helperCatalog
                        ->stripTags($downloads->getPurchased()->getProductName());
                    $eachDownloads["status"] = $downloads->getStatus();
                    if ($downloads->getNumberOfDownloadsBought()) {
                        $eachDownloads["remainingDownloads"] =
                        $downloads->getNumberOfDownloadsBought() - $downloads->getNumberOfDownloadsUsed();
                    } else {
                        $eachDownloads["remainingDownloads"] = __("Unlimited");
                    }

                    if ($this->canReorder($_order) == 1) {
                        $eachDownloads["canReorder"] = $this->canReorder($_order);
                    } else {
                        $eachDownloads["canReorder"] = 0;
                    }

                    $allDownloads[] = $eachDownloads;
                }
                $returnArray["allDownloads"] = $allDownloads;
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
