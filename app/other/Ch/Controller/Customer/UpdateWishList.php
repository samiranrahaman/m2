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
class UpdateWishList extends \Custom\Chharo\Controller\ApiController
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
            $customerId = $this->getRequest()->getPost("customerId");
            $itemData = $this->getRequest()->getPost("itemData");
            $itemData = json_decode($itemData);

            //test data
            // json_decode(
            //     json_encode(
            //         [
            //             ["id"=>1,"description"=>"this is test","qty"=>0],
            //             ["id"=>2,"description"=>"this is test","qty"=>3],
            //             ["id"=>3,"description"=>"this is test","qty"=>3]
            //         ]
            //     )
            // );
            try {
                $wishlist = $this->_objectManager
                    ->create("\Magento\Wishlist\Model\Wishlist")
                    ->loadByCustomerId($customerId, true);
                $wishlistHelper = $this->_objectManager
                    ->create("\Magento\Wishlist\Helper\Data");
                $updatedItems = 0;
                foreach ($itemData as $eachItem) {
                    $item = $this->_objectManager
                        ->create("\Magento\Wishlist\Model\Item")
                        ->load($eachItem->id);
                    
                    if ($item->getWishlistId() != $wishlist->getId()) {
                        continue;
                    }
                    $description = (string)$eachItem->description;

                    if ($description == $wishlistHelper->defaultCommentString()
                    ) {
                        $description = "";
                    } elseif (!strlen($description)) {
                        $description = $item->getDescription();
                    }

                    $qty = null;

                    if (isset($eachItem->qty)) {
                        $qty = $eachItem->qty;
                    }

                    if (is_null($qty)) {
                        $qty = $item->getQty();
                        if (!$qty) {
                            $qty = 1;
                        }
                    } elseif (0 == $qty) {
                        try {
                            $item->delete();
                        } catch (\Exception $e) {
                            $returnArray = [];
                            $returnArray["success"] = 0;
                            $returnArray["message"] =__("Can't delete item from wishlist");
                            return $this->getJsonResponse($returnArray);
                        }
                    }

                    if (($item->getDescription() == $description) && ($item->getQty() == $qty)) {
                        continue;
                    }
                    try {
                        $item->setDescription($description)->setQty($qty)->save();
                        $updatedItems++;
                    } catch (\Exception $e) {
                        $returnArray = [];
                        $returnArray["success"] = 0;
                        $returnArray["message"] =
                        __(
                            "Can't save description %1",
                            $this->_helperCatalog->escapeHtml($description)
                        );
                        return $this->getJsonResponse($returnArray);
                    }
                }

                if ($updatedItems) {
                    try {
                        $wishlist->save();
                        $wishlistHelper->calculate();
                    } catch (\Exception $e) {
                        $returnArray = [];
                        $returnArray["success"] = 0;
                        $returnArray["message"] = __("Can't update wishlist");
                        return $this->getJsonResponse($returnArray);
                    }
                }

                $returnArray["success"] = 1;
                $returnArray["message"] =__("Wishlist updated successfully");
                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
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
