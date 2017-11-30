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
class ViewInvoice extends AbstractMarketplace
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
                $customerId = $this->getRequest()->getPost("customerId");
                $incrementId = $this->getRequest()->getPost("incrementId");
                $invoiceId = $this->getRequest()->getPost("invoiceId");
                /**
                 * $initialEnvironmentInfo store emulation start
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $helper = $this->_marketplaceHelper;
                $order = $this->_orderFactory->create()->loadByIncrementId($incrementId);
                $invoice = $this->_objectManager->create("\Magento\Sales\Model\Order\InvoiceFactory")
                ->create()
                ->load($invoiceId);
                $orderId = $order->getId();
                if ($order->getPayment()) {
                    $paymentCode = $order->getPayment()->getMethod();
                    $payment_method = $order->getPayment()->getConfigData("title");
                }
                $invoiceStatus = "";
                if ($invoice->getState() == 1) {
                    $invoiceStatus = __("Pending");
                } elseif ($invoice->getState() == 2) {
                    $invoiceStatus = __("Paid");
                } elseif ($invoice->getState() == 3) {
                    $invoiceStatus = __("Canceled");
                }
                $marketplaceOrders = $this->_objectManager->create("Custom\Marketplace\Model\OrdersFactory")
                ->create()
                ->getCollection()
                ->addFieldToFilter("order_id", $orderId)
                ->addFieldToFilter("seller_id", $customerId);

                if (count($marketplaceOrders)) {
                    $returnArray["mainHeading"] = __("View Invoice Details");
                    $returnArray["sendmailAction"] = __("Send Email To Customer");
                    $returnArray["sendmailWarning"] = __("Are you sure you want to send order email to customer?");
                    $returnArray["subHeading"] = __("Invoice #%1 - %2 | %3", $invoice->getIncrementId(), $invoiceStatus, $invoice->getCreatedAtStoreDate());
                    $returnArray["orderData"]["title"] = __("Order Information");
                    $returnArray["orderData"]["label"] = __("Order # %1", $order->getIncrementId());
                    $returnArray["orderData"]["statusLabel"] = __("Order Status");
                    $returnArray["orderData"]["statusValue"] = ucfirst($order->getStatus());
                    $returnArray["orderData"]["dateLabel"] = __("Order Date");
                    $returnArray["orderData"]["dateValue"] = $order->getCreatedAt();

                    // Buyer Data
                    $returnArray["buyerData"]["title"] = __("Buyer Information");
                    $returnArray["buyerData"]["nameLabel"] = __("Customer Name").": ";
                    $returnArray["buyerData"]["nameValue"] = $order->getCustomerName();
                    $returnArray["buyerData"]["emailLabel"] = __("Email").": ";
                    $returnArray["buyerData"]["emailValue"] = $order->getCustomerEmail();

                    // Shipping Address Data
                    if (!$order->getIsVirtual()) {
                        $returnArray["shippingAddressData"]["title"] = __("Shipping Address");
                        $shippingAddress = $order->getShippingAddress();
                        $shippingAddressData[] = $shippingAddress->getFirstname()." ".$shippingAddress->getLastname();
                        $shippingAddressData[] = $shippingAddress->getStreet()[0];
                        if (count($shippingAddress->getStreet()) > 1) {
                            if ($shippingAddress->getStreet()[1]) {
                                $shippingAddressData[] = $shippingAddress->getStreet()[1];
                            }
                        }
                        $shippingAddressData[] = $shippingAddress->getCity().", ".$shippingAddress->getRegion().", ".$shippingAddress->getPostcode();
                        $shippingAddressData[] = $this->_objectManager->create("Magento\Directory\Model\CountryFactory")->create()
                        ->load($shippingAddress->getCountryId())->getName();
                        $shippingAddressData[] = "T: ".$shippingAddress->getTelephone();
                        $returnArray["shippingAddressData"]["address"] = $shippingAddressData;

                    // Shipping Method Data
                        $returnArray["shippingMethodData"]["title"] = __("Shipping Information");
                        if ($order->getShippingDescription()) {
                            $returnArray["shippingMethodData"]["method"] = $this->_helperCatalog->stripTags($order->getShippingDescription());
                        } else {
                            $returnArray["shippingMethodData"]["method"] = __("No shipping information available");
                        }
                    }

                    // Billing Address Data
                    $returnArray["billingAddressData"]["title"] = __("Billing Address");
                    $billingAddress = $order->getBillingAddress();
                    $billingAddressData[] = $billingAddress->getFirstname()." ".$billingAddress->getLastname();
                    $billingAddressData[] = $billingAddress->getStreet()[0];
                    if (count($billingAddress->getStreet()) > 1) {
                        if ($billingAddress->getStreet()[1]) {
                            $billingAddressData[] = $billingAddress->getStreet()[1];
                        }
                    }
                    $billingAddressData[] = $billingAddress->getCity().", ".$billingAddress->getRegion().", ".$billingAddress->getPostcode();
                    $billingAddressData[] = $this->_objectManager->create("Magento\Directory\Model\CountryFactory")->create()->load($billingAddress->getCountryId())->getName();
                    $billingAddressData[] = "T: ".$billingAddress->getTelephone();
                    $returnArray["billingAddressData"]["address"] = $billingAddressData;

                    // Payment Method Data
                    $returnArray["paymentMethodData"]["title"] = __("Payment Method");
                    $returnArray["paymentMethodData"]["method"] = $order->getPayment()->getMethodInstance()->getTitle();

                    // Item List
                    $itemCollection = $order->getAllVisibleItems();
                    $_count = count($itemCollection);
                    $subtotal = 0;
                    $vendorSubtotal = 0;
                    $totaltax = 0;
                    $adminSubtotal = 0;
                    $shippingamount = 0;
                    $codchargesTotal = 0;
                    foreach ($itemCollection as $_item) {
                        $eachItem = [];
                        $rowTotal = 0;
                        $availableSellerItem = 0;
                        $shippingcharges = 0;
                        $itemPrice = 0;
                        $sellerItemCost = 0;
                        $totaltaxPeritem = 0;
                        $codchargesPeritem = 0;
                        $sellerItemCommission = 0;
                        $sellerOrderslist = $this->_objectManager->create("Custom\Marketplace\Model\SaleslistFactory")
                        ->create()->getCollection()
                        ->addFieldToFilter("seller_id", $customerId)
                        ->addFieldToFilter("order_id", $orderId)
                        ->addFieldToFilter("mageproduct_id", $_item->getProductId())
                        ->addFieldToFilter("order_item_id", $_item->getItemId())
                        ->setOrder("order_id", "DESC");

                        foreach ($sellerOrderslist as $sellerItem) {
                            $availableSellerItem = 1;
                            $totalamount = $sellerItem->getTotalAmount();
                            $sellerItemCost = $sellerItem->getActualSellerAmount();
                            $sellerItemCommission = $sellerItem->getTotalCommision();
                            $shippingcharges = $sellerItem->getShippingCharges();
                            $itemPrice = $sellerItem->getMageproPrice();
                            $totaltaxPeritem = $sellerItem->getTotalTax();
                            $codchargesPeritem = $sellerItem->getCodCharges();
                        }
                        if ($availableSellerItem == 1) {
                            $sellerItemQty = $_item->getQtyOrdered();
                            $rowTotal = $itemPrice*$sellerItemQty;
                            $vendorSubtotal = $vendorSubtotal + $sellerItemCost;
                            $subtotal = $subtotal + $rowTotal;
                            $adminSubtotal = $adminSubtotal + $sellerItemCommission;
                            $totaltax = $totaltax + $totaltaxPeritem;
                            $codchargesTotal = $codchargesTotal + $codchargesPeritem;
                            $shippingamount = $shippingamount + $shippingcharges;
                            $result = [];
                            if ($options = $_item->getProductOptions()) {
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
                            $eachItem["productName"] = $_item->getName();
                            if ($_options = $result) {
                                foreach ($_options as $_option) {
                                    $eachOption = [];
                                    $eachOption["label"] = $this->_helperCatalog->stripTags($_option["label"]);
                                    $eachOption["value"] = $_option["value"];
                                    $eachItem["option"][] = $eachOption;
                                }
                            }
                            $eachItem["price"] = $this->_helperCatalog->stripTags($order->formatPrice($_item->getPrice()));
                            $eachItem["qty"]["Ordered"] = $_item->getQtyOrdered()*1;
                            $eachItem["qty"]["Invoiced"] = $_item->getQtyInvoiced()*1;
                            $eachItem["qty"]["Shipped"] = $_item->getQtyShipped()*1;
                            $eachItem["qty"]["Canceled"] = $_item->getQtyCanceled()*1;
                            $eachItem["qty"]["Refunded"] = $_item->getQtyRefunded()*1;
                            $eachItem["subTotal"] = $this->_helperCatalog->stripTags($order->formatPrice($rowTotal));
                            if ($paymentCode == "mpcashondelivery") {
                                $eachItem["codCharges"] = $this->_helperCatalog->stripTags($order->formatPrice($codchargesPeritem));
                            }
                            $eachItem["adminComission"] = $this->_helperCatalog->stripTags($order->formatPrice($sellerItemCommission));
                            $eachItem["vendorTotal"] = $this->_helperCatalog->stripTags($order->formatPrice($sellerItemCost));
                            $returnArray["items"][] = $eachItem;
                        }
                    }
                    $returnArray["subtotal"]["title"] = __("Subtotal");
                    $returnArray["subtotal"]["value"] = $this->_helperCatalog->stripTags($order->formatPrice($subtotal));
                    $returnArray["shipping"]["title"] = __("Shipping & Handling");
                    $returnArray["shipping"]["value"] = $this->_helperCatalog->stripTags($order->formatPrice($shippingamount));
                    $returnArray["tax"]["title"] = __("Total Tax");
                    $returnArray["tax"]["value"] = $this->_helperCatalog->stripTags($order->formatPrice($totaltax));
                    $admintotaltax = 0;
                    $vendortotaltax = 0;
                    if (!$this->_marketplaceHelper->getConfigTaxManage()) {
                        $admintotaltax = $totaltax;
                    } else {
                        $vendortotaltax = $totaltax;
                    }
                    if ($paymentCode == "mpcashondelivery") {
                        $returnArray["cod"]["title"] = __("Total COD Charges");
                        $returnArray["cod"]["value"] = $this->_helperCatalog->stripTags($order->formatPrice($codchargesTotal));
                    }
                    $returnArray["totalOrderedAmount"]["title"] = __("Total Ordered Amount");
                    $returnArray["totalOrderedAmount"]["value"] = $this->_helperCatalog->stripTags($order->formatPrice($subtotal+$shippingamount+$codchargesTotal+$totaltax));
                    $returnArray["totalVendorAmount"]["title"] = __("Total Vendor Amount");
                    $returnArray["totalVendorAmount"]["value"] = $this->_helperCatalog->stripTags($order->formatPrice($vendorSubtotal+$shippingamount+$codchargesTotal+$vendortotaltax));
                    $returnArray["totalAdminComission"]["title"] = __("Total Admin Commission");
                    $returnArray["totalAdminComission"]["value"] = $this->_helperCatalog->stripTags($order->formatPrice($adminSubtotal+$admintotaltax));
                }
                
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
