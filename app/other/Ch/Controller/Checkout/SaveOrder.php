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
class SaveOrder extends AbstractCheckout
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
            if ($storeId == '') {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                /**
                 * $initialEnvironmentInfo store emulation start.
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                if ($customerId != '') {
                    $quoteCollection = $this->_quoteFactory
                        ->create()->getCollection();
                    $quoteCollection->addFieldToFilter('customer_id', $customerId);
                    $quoteCollection->addOrder('updated_at', 'desc');
                    $this->_quote = $quoteCollection->getFirstItem();
                    $quoteId = $this->_quote->getId();
                }
                if ($quoteId != '') {
                    $this->_quote = $this->_quoteFactory
                        ->create()
                        ->setStoreId($storeId)->load($quoteId);
                }
                $this->_quote->setReservedOrderId(null);
                $method = $this->getRequest()->getPost('method');

                // saving order
                $orderData = [];
                $orderData['method'] = $method;
                $ccCid = $this->getRequest()->getPost('cc_cid');
                $ccExpMonth = $this->getRequest()->getPost('cc_exp_month');
                $ccExpYear = $this->getRequest()->getPost('cc_exp_year');
                $ccNumber = $this->getRequest()->getPost('cc_number');
                $ccType = $this->getRequest()->getPost('cc_type');
                if ($ccCid != '' 
                    && $ccExpMonth != '' 
                    && $ccExpYear != '' 
                    && $ccNumber != '' 
                    && $ccType != ''
                ) {
                    if ($ccCid != '') {
                        $orderData['cc_cid'] = $ccCid;
                    }
                    if ($ccExpMonth != '') {
                        $orderData['cc_exp_month'] = $ccExpMonth;
                    }
                    if ($ccExpYear != '') {
                        $orderData['cc_exp_year'] = $ccExpYear;
                    }
                    if ($ccNumber != '') {
                        $orderData['cc_number'] = $ccNumber;
                    }
                    if ($ccType != '') {
                        $orderData['cc_type'] = $ccType;
                    }
                    $orderData['checks'] =
                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_CHECKOUT |
                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_COUNTRY |
                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_CURRENCY |
                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX |
                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_ZERO_TOTAL;
                    $this->_quote->getPayment()->importData($orderData);
                }
                if ($this->_quote->getCheckoutMethod() == 'customer') {
                    $customer = $this->_customerFactory
                        ->create()
                        ->load($customerId);
                }

                if ($this->_quote->getCheckoutMethod() == 'guest' 
                    && !$this->_objectManager->create(
                        "\Magento\Checkout\Helper\Data"
                    )->isAllowedGuestCheckout($this->_quote, $this->_quote->getStoreId())
                ) {
                    $returnArray = [
                        'error' => 1,
                        'message' => __('Guest Checkout is not Enabled'),
                    ];

                    return $this->getJsonResponse($returnArray);
                }
                $isNewCustomer = 0;
                if ($this->_quote->getCheckoutMethod() == 'register') {
                    $this->_prepareNewCustomerQuote();
                    $isNewCustomer = 1;
                } elseif ($this->_quote->getCheckoutMethod() == 'guest') {
                    $this->_prepareGuestQuote();
                } else {
                    $this->_prepareCustomerQuote();
                }

                $this->_quote->collectTotals()->save();

                $order = $this->_objectManager
                    ->create("\Magento\Quote\Model\QuoteManagement")
                    ->submit($this->_quote);

                if ($isNewCustomer) {
                    $customerResource->_involveNewCustomer();
                }

                if ($order) {
                    $this->_eventManager->dispatch(
                        'checkout_type_onepage_save_order_after',
                        ['order' => $order, 'quote' => $this->_quote]
                    );

                    try {
                        $this->_objectManager
                            ->create(
                                "\Magento\Sales\Model\Order\Email\Sender\OrderSender"
                            )
                            ->send($order);
                    } catch (\Exception $e) {
                        $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                    }
                }
                $this->_eventManager->dispatch(
                    'checkout_submit_all_after',
                    ['order' => $order, 'quote' => $this->_quote]
                );
                $this->_quote->removeAllItems();
                $this->_quote->save();
                $this->_quote->collectTotals()->save();
                $canReorder = 0;

                if ($this->canReorder($order) == 1) {
                    $canReorder = $this->canReorder($order);
                } else {
                    $canReorder = 0;
                }

                $returnArray = [
                    'error' => 0,
                    'orderId' => $order->getId(),
                    'incrementId' => $order->getIncrementId(),
                    'canReorder' => $canReorder,
                ];

                $this->_quote->collectTotals()->save();

                /*
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                $returnArray['success'] = 1;

                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray['success'] = 0;
                $returnArray['message'] = __('Invalid Request.');
                $this->_logger->critical($e);

                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray['success'] = 0;
            $returnArray['message'] = __('Invalid Request.');

            return $this->getJsonResponse($returnArray);
        }
    }

    /**
     * Prepare quote for customer registration and customer order submit.
     */
    protected function _prepareNewCustomerQuote()
    {
        $billing = $this->_quote->getBillingAddress();
        $shipping = $this->_quote->isVirtual() ? null : $this->_quote->getShippingAddress();

        $customer = $this->_quote->getCustomer();
        $customerBillingData = $billing->exportCustomerAddress();
        $dataArray = $this->_objectCopyService
            ->getDataFromFieldset(
                'checkout_onepage_quote',
                'to_customer',
                $this->_quote
            );
        $this->_dataObjectHelper->populateWithArray(
            $customer,
            $dataArray,
            '\Magento\Customer\Api\Data\CustomerInterface'
        );
        $this->_quote->setCustomer($customer)->setCustomerId(true);

        $customerBillingData->setIsDefaultBilling(true);

        if ($shipping) {
            if (!$shipping->getSameAsBilling()) {
                $customerShippingData = $shipping->exportCustomerAddress();
                $customerShippingData->setIsDefaultShipping(true);
                $shipping->setCustomerAddressData($customerShippingData);
                // Add shipping address to quote since customer Data Object does not hold address information
                $this->_quote->addCustomerAddress($customerShippingData);
            } else {
                $shipping->setCustomerAddressData($customerBillingData);
                $customerBillingData->setIsDefaultShipping(true);
            }
        } else {
            $customerBillingData->setIsDefaultShipping(true);
        }
        $billing->setCustomerAddressData($customerBillingData);
        // TODO : Eventually need to remove this legacy hack
        // Add billing address to quote since customer Data Object does not hold address information
        $this->_quote->addCustomerAddress($customerBillingData);
    }

    /**
     * Prepare quote for guest checkout order submit.
     *
     * @return $this
     */
    protected function _prepareGuestQuote()
    {
        $this->_quote->setCustomerId(null)
            ->setCustomerEmail($this->_quote->getBillingAddress()->getEmail())
            ->setCustomerIsGuest(true)
            ->setCustomerGroupId(\Magento\Customer\Api\Data\GroupInterface::NOT_LOGGED_IN_ID);

        return $this;
    }

    /**
     * Prepare quote for customer order submit.
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _prepareCustomerQuote()
    {
        $billing = $this->_quote->getBillingAddress();
        $shipping = $this->_quote->isVirtual() ? null : $this->_quote->getShippingAddress();

        $customer = $this->_customerRepository->getById($this->getRequest()->getPost('customerId'));
        $hasDefaultBilling = (bool) $customer->getDefaultBilling();
        $hasDefaultShipping = (bool) $customer->getDefaultShipping();

        if ($shipping && !$shipping->getSameAsBilling() 
            && (!$shipping->getCustomerId() || $shipping->getSaveInAddressBook())
        ) {
            $shippingAddress = $shipping->exportCustomerAddress();
            if (!$hasDefaultShipping) {
                //Make provided address as default shipping address
                $shippingAddress->setIsDefaultShipping(true);
                $hasDefaultShipping = true;
            }
            $this->_quote->addCustomerAddress($shippingAddress);
            $shipping->setCustomerAddressData($shippingAddress);
        }

        if (!$billing->getCustomerId() || $billing->getSaveInAddressBook()) {
            $billingAddress = $billing->exportCustomerAddress();
            if (!$hasDefaultBilling) {
                //Make provided address as default shipping address
                if (!$hasDefaultShipping) {
                    //Make provided address as default shipping address
                    $billingAddress->setIsDefaultShipping(true);
                }
                $billingAddress->setIsDefaultBilling(true);
            }
            $this->_quote->addCustomerAddress($billingAddress);
            $billing->setCustomerAddressData($billingAddress);
        }
    }

    /**
     * Involve new customer to system.
     *
     * @return $this
     */
    protected function _involveNewCustomer()
    {
        $customer = $this->_quote->getCustomer();
        $confirmationStatus = $this->_objectManager
            ->create("\Magento\Customer\Model\AccountManagement")
            ->getConfirmationStatus($customer->getId());

        if ($confirmationStatus === \Magento\Customer\Model\AccountManagement::ACCOUNT_CONFIRMATION_REQUIRED) {
            $url = $this->_customerUrl->getEmailConfirmationUrl($customer->getEmail());
        }
    }
}
