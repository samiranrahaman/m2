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
class GetOrderDetail extends \Custom\Chharo\Controller\ApiController
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
            $incrementId = $this->getRequest()->getPost("incrementId");

            try {
                $_order = $this->_objectManager
                    ->create("\Magento\Sales\Model\Order")
                    ->loadByIncrementId($incrementId);
                if (!$_order || !$_order->getId()) {
                    $returnArray["success"] = 0;
                    $returnArray["message"] = __("Invalid Order.");
                    return $this->getJsonResponse($returnArray);
                }
                $returnArray = [];
                $returnArray["incrementId"] = $_order->getRealOrderId();
                $returnArray["statusLabel"] = $_order->getStatusLabel();
                $returnArray["orderDate"] = $this->_helperCatalog
                    ->formatDate($_order->getCreatedAt(), "long");
                if ($_order->getShippingAddressId()) {
                    // shipping address
                    $shippingAddress = $this->_objectManager
                        ->create("\Magento\Sales\Model\Order\Address")
                        ->load($_order->getShippingAddressId());

                    $shippingAddressData[] = $shippingAddress->getFirstname()." ".$shippingAddress->getLastname();

                    $shippingAddressData[] = $shippingAddress->getStreet()[0];
                    if (count($shippingAddress->getStreet()) > 1) {
                        if ($shippingAddress->getStreet()[1]) {
                            $shippingAddressData[] = $shippingAddress->getStreet()[1];
                        }
                    }

                    $shippingAddressData[] =
                    $shippingAddress->getCity().", ".$shippingAddress->getRegion().", ".$shippingAddress->getPostcode();
                    $shippingAddressData[] = $this->_objectManager
                        ->create("\Magento\Directory\Model\Country")
                        ->load($shippingAddress->getCountryId())->getName();

                    $shippingAddressData[] = "T: ".$shippingAddress->getTelephone();
                    $returnArray["shippingAddress"] = implode("\n", $shippingAddressData);
                    if ($_order->getShippingDescription()) {
                        $returnArray["shippingMethod"] = $this->_helperCatalog
                            ->stripTags($_order->getShippingDescription());
                    } else {
                        $returnArray["shippingMethod"] = __("No shipping information available");
                    }
                }
                // billing address
                $billingAddress = $this->_objectManager
                    ->create("\Magento\Sales\Model\Order\Address")
                    ->load($_order->getBillingAddressId());
                $billingAddressData[] = $billingAddress->getFirstname()." ".$billingAddress->getLastname();
                $billingAddressData[] = $billingAddress->getStreet()[0];
                if (count($billingAddress->getStreet()) > 1) {
                    if ($billingAddress->getStreet()[1]) {
                        $billingAddressData[] = $billingAddress->getStreet()[1];
                    }
                }
                $billingAddressData[] =
                $billingAddress->getCity().", ".$billingAddress->getRegion().", ".$billingAddress->getPostcode();
                $billingAddressData[] = $this->_objectManager
                    ->create("\Magento\Directory\Model\Country")
                    ->load($billingAddress->getCountryId())->getName();
                $billingAddressData[] = "T: ".$billingAddress->getTelephone();
                $returnArray["billingAddress"] = implode("\n", $billingAddressData);
                $returnArray["billingMethod"] = $_order->getPayment()->getMethodInstance()->getTitle();
                $itemCollection = $_order->getAllVisibleItems();

                foreach ($itemCollection as $item) {
                    $eachItem = [];
                    $eachItem["name"] = $item->getName();
                    $result = [];
                    if ($options = $item->getProductOptions()) {
                        if (isset($options["options"])) {
                            $result = array_merge($result, $options["options"]);
                        }
                        if (isset($options["additional_options"])) {
                            $result = array_merge($result, $options["additional_options"]);
                        }
                        if (isset($options["attributes_info"])) {
                            $result = array_merge($result, $options["attributes_info"]);
                        }
                    }
                    if ($result) {
                        foreach ($result as $_option) {
                            $eachOption = [];
                            $eachOption["label"] = $this->_helperCatalog
                                ->stripTags($_option["label"]);
                            $eachOption["value"] = $_option["value"];
                            $eachItem["option"][] = $eachOption;
                        }
                    }
                    $eachItem["sku"] = $this->_helperCatalog->stripTags(
                        $this->_helperCatalog
                            ->coreString->splitInjection(
                                $item->getSku()
                            )
                    );
                    $eachItem["price"] = $this->_helperCatalog
                        ->stripTags($_order->formatPrice($item->getPrice()));
                    $eachItem["qty"]["Ordered"] = $item->getQtyOrdered()*1;
                    $eachItem["qty"]["Shipped"] = $item->getQtyShipped()*1;
                    $eachItem["qty"]["Canceled"] = $item->getQtyCanceled()*1;
                    $eachItem["qty"]["Refunded"] = $item->getQtyRefunded()*1;
                    $eachItem["subTotal"] = $this->_helperCatalog
                        ->stripTags($_order->formatPrice($item->getRowTotal()));
                    $returnArray["items"][] = $eachItem;
                }
                $_totals = [];
                $_totals["subtotal"] = [
                    "value" => $this->_helperCatalog->stripTags($_order->formatPrice($_order->getSubtotal())),

                    "label" => __("Subtotal")
                ];

                if (!$_order->getIsVirtual() 
                    && ((float) $_order->getShippingAmount() || $_order->getShippingDescription())
                ) {
                    $_totals["shipping"] = [
                        "value" => $this->_helperCatalog->stripTags(
                            $_order->formatPrice(
                                $_order->getShippingAmount()
                            )
                        ),
                        "label" => __("Shipping & Handling")
                    ];
                }
                if (((float)$_order->getDiscountAmount()) != 0) {
                    if ($_order->getDiscountDescription()) {
                        $discountLabel = $this->_helperCatalog
                            ->__("Discount (%s)", $_order->getDiscountDescription());
                    } else {
                        $discountLabel = __("Discount");
                    }
                    $_totals["discount"] = [
                        "value" => $this->_helperCatalog
                            ->stripTags(
                                $_order->formatPrice(
                                    $_order->getDiscountAmount()
                                )
                            ),
                        "label" => $discountLabel
                    ];
                }
                if ($_order->getTaxAmount()) {
                    $_totals["tax"] = [
                        "value" => $this->_helperCatalog->stripTags($_order->formatPrice($_order->getTaxAmount())),

                        "label" => __("Tax")
                    ];
                }
                $_totals["grandTotal"] = [
                    "value" => $this->_helperCatalog->stripTags($_order->formatPrice($_order->getGrandTotal())),
                    "label" => __("Grand Total")
                ];
                if ($_order->isCurrencyDifferent()) {
                    $_totals["baseGrandtotal"] = [
                        "value" => $this->_helperCatalog->stripTags(
                            $_order->formatBasePrice(
                                $_order->getBaseGrandTotal()
                            )
                        ),
                        "label" => __("Grand Total to be Charged")
                    ];
                }
                $returnArray["totals"] = $_totals;
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
