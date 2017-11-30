<?php
/**
 * Custom Software.
 *
 * @category Custom
 *
 * @author    Custom
 * @copyright Copyright (c) 2010-2017 Custom Software Private Limited (https://Custom.com)
 * @license   https://store.Custom.com/license.html
 */

namespace Custom\Chharo\Controller\Checkout;

/**
 * Chharo API Checkout controller.
 */
class GetOrderReviewData extends AbstractCheckout
{
    /**
     * execute category list.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $storeId = $this->getRequest()->getPost('storeId');
            $customerId = $this->getRequest()->getPost('customerId');
            $quoteId = $this->getRequest()->getPost('quoteId');
            $quote = null;
            $returnArray = [];
            if ($storeId == '') {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                /**
                 * $initialEnvironmentInfo store emulation start.
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                if ($customerId != '') {
                    $quoteCollection = $this->_quoteFactory->create()->getCollection();
                    $quoteCollection->addFieldToFilter('customer_id', $customerId);
                    $quoteCollection->addOrder('updated_at', 'desc');
                    $quote = $quoteCollection->getFirstItem();
                }
                if ($quoteId != '') {
                    $quote = $this->_quoteFactory->create()
                        ->setStoreId(
                            $storeId
                        )->load($quoteId);
                }

                $orderReviewData = [];

                //saving shipping
                $shippingMethod = $this->getRequest()->getPost('shippingMethod');
                if ($shippingMethod != '') {
                    $rate = $quote->getShippingAddress()->getShippingRateByCode($shippingMethod);
                    if (!$rate) {
                        return $this->getJsonResponse(
                            [
                                'error' => 1,
                                'message' => __('Invalid shipping method.'),
                            ]
                        );
                    }
                    $quote->getShippingAddress()->setShippingMethod($shippingMethod);
                }
                //saving payment
                $method = $this->getRequest()->getPost('method');
                if ($method != '') {
                    $paymentData = [];
                    $paymentData['method'] = $method;
                    $ccCid = $this->getRequest()->getPost('cc_cid');
                    if (isset($ccCid) && $ccCid != '') {
                        $paymentData['cc_cid'] = $ccCid;
                    }
                    $ccExpMonth = $this->getRequest()->getPost('cc_exp_month');
                    if (isset($ccExpMonth) && $ccExpMonth != '') {
                        $paymentData['cc_exp_month'] = $ccExpMonth;
                    }
                    $ccExpYear = $this->getRequest()->getPost('cc_exp_year');
                    if (isset($ccExpYear) && $ccExpYear != '') {
                        $paymentData['cc_exp_year'] = $ccExpYear;
                    }
                    $ccNumber = $this->getRequest()->getPost('cc_number');
                    if (isset($ccNumber) && $ccNumber != '') {
                        $paymentData['cc_number'] = $ccNumber;
                    }
                    $ccType = $this->getRequest()->getPost('cc_type');
                    if (isset($ccType) && $ccType != '') {
                        $paymentData['cc_type'] = $ccType;
                    }

                    if ($quote->isVirtual()) {
                        $quote->getBillingAddress()->setPaymentMethod(isset($method) ? $method : null);
                    } else {
                        $quote->getShippingAddress()->setPaymentMethod(isset($method) ? $method : null);
                    }

                    if (!$quote->isVirtual() && $quote->getShippingAddress()) {
                        $quote->getShippingAddress()->setCollectShippingRates(true);
                    }

                    $paymentData['checks'] =
                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_CHECKOUT |
                     \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_COUNTRY |
                     \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_CURRENCY |
                     \Magento\Payment\Model\Method\AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX |
                     \Magento\Payment\Model\Method\AbstractMethod::CHECK_ZERO_TOTAL;
                    $payment = $quote->getPayment()->importData($paymentData);
                    $quote->save();
                }

                foreach ($quote->getAllVisibleItems() as $_item) {
                    $eachItem = [];
                    $eachItem['productName'] = $this->_helperCatalog->stripTags($_item->getName());
                    $customoptions = $_item->getProduct()->getTypeInstance(true)->getOrderOptions($_item->getProduct());
                    $result = [];
                    if ($customoptions) {
                        if (isset($customoptions['options'])) {
                            $result = array_merge($result, $customoptions['options']);
                        }
                        if (isset($customoptions['additional_options'])) {
                            $result = array_merge($result, $customoptions['additional_options']);
                        }
                        if (isset($customoptions['attributes_info'])) {
                            $result = array_merge($result, $customoptions['attributes_info']);
                        }
                    }
                    if ($result) {
                        foreach ($result as $_option) {
                            $eachOption = [];
                            $eachOption['label'] = $this->_helperCatalog->stripTags($_option['label']);
                            $eachOption['value'] = $_option['value'];
                            $eachItem['option'][] = $eachOption;
                        }
                    }
                    $eachItem['price'] = $this->_helperCatalog
                        ->stripTags(
                            $this->_priceHelper->currency(
                                $_item->getCalculationPrice()
                            )
                        );
                    $eachItem['unformatedPrice'] = $_item->getCalculationPrice();
                    $eachItem['qty'] = $_item->getQty();
                    $eachItem['sku'] = $_item->getSku();
                    $eachItem['subTotal'] = $this->_helperCatalog->stripTags(
                        $this->_priceHelper->currency(
                            $_item->getRowTotal()
                        )
                    );
                    $orderReviewData['items'][] = $eachItem;
                }
                $totals = $quote->getTotals();
                if (isset($totals['subtotal'])) {
                    $subtotal = $totals['subtotal'];
                    $orderReviewData['subtotal']['title'] = $subtotal->getTitle();
                    $orderReviewData['subtotal']['value'] = $this->_helperCatalog
                        ->stripTags(
                            $this->_priceHelper->currency(
                                $subtotal->getValue()
                            )
                        );
                    $orderReviewData['subtotal']['unformatedValue'] = $subtotal->getValue();
                }
                if (isset($totals['discount'])) {
                    $discount = $totals['discount'];
                    $orderReviewData['discount']['title'] = $discount->getTitle();
                    $orderReviewData['discount']['value'] =
                    $this->_helperCatalog->stripTags(
                        $this->_priceHelper->currency(
                            $discount->getValue()
                        )
                    );
                    $orderReviewData['discount']['unformatedValue'] = $discount->getValue();
                }
                if (isset($totals['tax'])) {
                    $tax = $totals['tax'];
                    $orderReviewData['tax']['title'] = $tax->getTitle();
                    $orderReviewData['tax']['value'] = $this->_helperCatalog
                        ->stripTags(
                            $this->_priceHelper->currency(
                                $tax->getValue()
                            )
                        );
                    $orderReviewData['tax']['unformatedValue'] = $tax->getValue();
                }
                if (isset($totals['shipping'])) {
                    $shipping = $totals['shipping'];
                    $orderReviewData['shipping']['title'] = $shipping->getTitle();
                    $orderReviewData['shipping']['value'] = $this->_helperCatalog->stripTags(
                        $this->_priceHelper->currency(
                            $shipping->getValue()
                        )
                    );
                    $orderReviewData['shipping']['unformatedValue'] = $shipping->getValue();
                }
                if (isset($totals['grand_total'])) {
                    $grandtotal = $totals['grand_total'];
                    $orderReviewData['grandtotal']['title'] = $grandtotal->getTitle();
                    $orderReviewData['grandtotal']['value'] = $this->_helperCatalog->stripTags(
                        $this->_priceHelper->currency(
                            $grandtotal->getValue()
                        )
                    );
                    $orderReviewData['grandtotal']['unformatedValue'] = $grandtotal->getValue();
                }
                $orderReviewData['currencyCode'] = $this->_storeManager
                    ->getStore()->getCurrentCurrencyCode();
                $returnArray['billingMethod'] = strip_tags($quote->getPayment()->getMethodInstance()->getConfigData('title'));

                if (!$quote->isVirtual() && $quote->getShippingAddress() && $quote->getShippingAddress()->getId()) {
                    $returnArray['shippingMethod'] = $quote->getShippingAddress()->getShippingDescription();
                    $returnArray['shippingAddress'] = $this->getFormattedAddress($quote->getShippingAddress()->toArray());
                }
                $returnArray['billingAddress'] = $this->getFormattedAddress($quote->getBillingAddress()->toArray());

                $returnArray['orderReviewData'] = $orderReviewData;

                /*
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                $returnArray['success'] = 1;

                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $this->createLog('Chharo Exception log for class: '.get_class($this).' : '.$e->getMessage(), (array) $e->getTrace());
                $returnArray['success'] = 0;
                $returnArray['message'] = __('Invalid Request.');

                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray['success'] = 0;
            $returnArray['message'] = __('Invalid Request.');

            return $this->getJsonResponse($returnArray);
        }
    }

    public function getFormattedAddress($address)
    {
        if (count($address) > 0) {
            $country = '';
            $region = '';
            $addressArray[0] = $address['firstname'].' '.$address['lastname'];
            $addressArray[1] = $address['street'];
            $addressArray[2] = $address['city'];

            if (isset($address['region']) && $address['region']) {
                $region = $address['region'];
            } else {
                $region = $this->_objectManager->create("\Magento\Directory\Model\RegionFactory")->create()->load($address['region_id'])->getName();
            }

            if (isset($address['country']) && $address['country']) {
                $country = $address['country'];
            } else {
                $country = $this->_objectManager->create("\Magento\Directory\Model\CountryFactory")->create()->load($address['country_id'])->getName();
            }

            $addressArray[3] = $region;
            $addressArray[4] = $address['postcode'];
            $addressArray[5] = $country;
            $addressArray[6] = 'T:'.$address['telephone'];
            if (isset($address['fax'])) {
                $addressArray[7] = 'F:'.$address['fax'];
            }

            return implode('<br>', $addressArray);
        } else {
            return;
        }
    }
}
