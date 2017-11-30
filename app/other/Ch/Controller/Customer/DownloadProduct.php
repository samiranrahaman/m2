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
class DownloadProduct extends \Custom\Chharo\Controller\ApiController
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
     * execute download product links etc
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $customerId = $this->getRequest()->getPost("customerId");
            $hash = $this->getRequest()->getPost("hash");
            $sessionId = $this->getRequest()->getPost("sessionId");
            
            try {
                $linkPurchasedItem = $this->_objectManager
                    ->create("\Magento\Downloadable\Model\Link\Purchased\Item")
                    ->load($hash, "link_hash");
                $downloadableHelper = $this->_objectManager
                    ->create("\Magento\Downloadable\Helper\Data");
                $downloadableHelperFile = $this->_objectManager
                    ->create("\Magento\Downloadable\Helper\File");

                if (!$linkPurchasedItem->getId()) {
                    $returnArray["success"] = 0;
                    $returnArray["message"] = __("Requested link does not exist.");
                    return $this->getJsonResponse($returnArray);
                }
                if (!$downloadableHelper->getIsShareable($linkPurchasedItem)) {
                    $linkPurchased = $this->_objectManager
                        ->create("\Magento\Downloadable\Model\Link\Purchased")
                        ->load($linkPurchasedItem->getPurchasedId());

                    if ($linkPurchased->getCustomerId() != $customerId) {
                        $returnArray["success"] = 0;
                        $returnArray["message"] = _("Requested link does not exist.");
                        return $this->getJsonResponse($returnArray);
                    }
                }
                $downloadsLeft = $linkPurchasedItem
                    ->getNumberOfDownloadsBought() - $linkPurchasedItem->getNumberOfDownloadsUsed();
                $status = $linkPurchasedItem->getStatus();
                if ($status ==\Magento\Downloadable\Model\Link\Purchased\Item::LINK_STATUS_AVAILABLE 
                    && (                    $downloadsLeft 
                    || $linkPurchasedItem->getNumberOfDownloadsBought() == 0)
                ) {
                    $resource = "";
                    $resourceType = "";
                    if ($linkPurchasedItem->getLinkType() ==\Magento\Downloadable\Helper\Download::LINK_TYPE_URL
                    ) {
                        $returnArray["url"] = $linkPurchasedItem->getLinkUrl();
                        $fileArray =
                        explode(DS, $linkPurchasedItem->getLinkUrl());
                        $returnArray["fileName"] = end($fileArray);
                    } elseif ($linkPurchasedItem->getLinkType() ==\Magento\Downloadable\Helper\Download::LINK_TYPE_FILE
                    ) {
                        $linkFile = $downloadableHelperFile
                            ->getFilePath(
                                $this->_objectManager
                                    ->create("\Magento\Downloadable\Model\Link")
                                    ->getBasePath(),
                                $linkPurchasedItem->getLinkFile()
                            );
                        $linkFile = $this->_helperCatalog->getBasePath().DS.$linkFile;
                        if (file_exists($linkFile)) {
                            $returnArray["url"] = $this->_helper->getPageUrl(
                                "chharohttp/download/index",
                                ["hash" => $hash, "sessionId" => $sessionId]
                            );
                            $fileArray = explode(DS, $linkFile);
                            $returnArray["fileName"] = end($fileArray);
                        } else {
                            $returnArray["success"] = 0;
                            $returnArray["message"] =
                            __(
                                "An error occurred while getting the requested content. Please contact the store owner."
                            );
                            return $this->getJsonResponse($returnArray);
                        }
                    }
                } elseif ($status == \Magento\Downloadable\Model\Link\Purchased\Item::LINK_STATUS_EXPIRED) {
                    $returnArray["success"] = 0;
                    $returnArray["message"] = __("The link has expired.");
                    return $this->getJsonResponse($returnArray);
                } elseif ($status == \Magento\Downloadable\Model\Link\Purchased\Item::LINK_STATUS_PENDING 
                    || $status == \Magento\Downloadable\Model\Link\Purchased\Item::LINK_STATUS_PAYMENT_REVIEW
                ) {
                    $returnArray["success"] = 0;
                    $returnArray["message"] = __("The link is not available.");
                    return $this->getJsonResponse($returnArray);
                } else {
                    $returnArray["success"] = 0;
                    $returnArray["message"] =
                    __("An error occurred while getting the requested content. Please contact the store owner.");
                    return $this->getJsonResponse($returnArray);
                }
                $returnArray["success"] = 1;
                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
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
