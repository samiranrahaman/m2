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
class GetStepThreenFourData extends AbstractCheckout
{
    /**
     * execute category list.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            // $adDa = json_encode(
            //     [
            //         "newAddress" => [
            //             "default_billing" => 1,
            //             "default_shipping" => 1,
            //             "country_id" => "AT",
            //             "region_id" => 102,
            //             "region" => "",
            //             "street"=>[
            //                 0 => "this is test",
            //                 1 => "this is too"
            //             ],
            //             "telephone" => 899978787,
            //             "firstName" => "Veronica",
            //             "lastName" => "Costello",
            //             "city" => "jhfgjgh",
            //             "postcode"=> "32009",
            //             "save_in_address_book"=>0,
            //             "fax" => "343",
            //             "company" => "Custom",
            //             "emailAddress" => "test@Custom.com"
            //         ],
            //         "use_for_shipping" =>1

            //     ]
            // );
			
			/*   $adDa = json_encode(
                 [
                     "newAddress" => [
                        "default_billing" => 1,
                        "default_shipping" => 1,
                        "country_id" => "AT",
                        "region_id" => 102,
                         "region" => "",
                         "street"=>[
                             0 => "this is test",
                            1 => "this is too"
                         ],
                         "telephone" => 899978787,
                         "firstName" => "Veronica",
                         "lastName" => "Costello",
                        "city" => "jhfgjgh",
                         "postcode"=> "32009",
                         "save_in_address_book"=>0,
                         "fax" => "343",
                         "company" => "Custom",
                         "emailAddress" => "test@Custom.com"
                     ],
                     "use_for_shipping" =>1

                ]
             ); */
            $storeId = $this->getRequest()->getPost('storeId');
            $returnArray = [];
            $extraInformation = '';
            if ($storeId == '') {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                /**
                 * $initialEnvironmentInfo store emulation start.
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $customerId = $this->getRequest()->getPost('customerId');
                $quote = null;
                if ($customerId != '') {
                    //get customer current quote collection
                    $quoteCollection = $this->_quoteFactory
                        ->create()
                        ->getCollection();
                    $quoteCollection
                    ->addFieldToFilter('customer_id', $customerId);

                    $quoteCollection->addOrder('updated_at', 'desc');

                    $quote = $quoteCollection->getFirstItem();
                    $quoteId = $quote->getId();
                }

                $quoteId = $this->getRequest()->getPost('quoteId');
                if ($quoteId != '') {
                    //load current quote
                    $quote = $this->_quoteFactory->create()
                        ->setStoreId($storeId)
                        ->load($quoteId);
                }

                $useForShipping = 0;
                //$this->getRequest()->setParam("billingData", $adDa);
                $billingData = $this->getRequest()->getParam('billingData');
                $billingData = json_decode($billingData);

                if (!empty($billingData)) {
                    $saveInAddressBook = 0;
                    if (isset($billingData->newAddress->save_in_address_book)) {
                        $saveInAddressBook = $billingData->newAddress->save_in_address_book;
                    }
                    $checkoutMethod = $this->getRequest()->getPost('checkoutMethod');
                    if ($checkoutMethod == 'register') {
                        $saveInAddressBook = 1;
                    }
                    if (isset($billingData->use_for_shipping) && $billingData->use_for_shipping != '') {
                        $useForShipping = $billingData->use_for_shipping;
                    }

                    $addressId = '';
                    if (isset($billingData->addressId) && $billingData->addressId != '') {
                        $addressId = $billingData->addressId;
                    }
                    $quote->setCheckoutMethod($checkoutMethod)->save();
                    $newAddress = '';
                    if (isset($billingData->newAddress) && $billingData->newAddress != '') {
                        if (!empty($billingData->newAddress)) {
                            $newAddress = $billingData->newAddress;
                        }
                    }
                    $address = $quote->getBillingAddress();
                    $addressForm = $this->_objectManager
                        ->create("\Magento\Customer\Model\Form");
                    $addressForm->setFormCode('customer_address_edit')->setEntityType('customer_address');
                    if (is_numeric($addressId)) {
                        $this->createLog('Billing Data : ', [$addressId]);

                        $customerAddress = $this->_objectManager
                            ->create("\Magento\Customer\Model\Address")
                            ->load($addressId)
                            ->getDataModel();
                        if ($customerAddress->getId()) {
                            if ($customerAddress->getCustomerId() != $quote->getCustomerId()) {
                                return $this->getJsonResponse(
                                    [
                                    'error' => 1,
                                    'message' => __('Customer Address is not valid.'),
                                    ]
                                );
                            }
                            $address->importCustomerAddressData($customerAddress)->setSaveInAddressBook(0);
                            $addressForm->setEntity($address);
                            $addressErrors = $addressForm->validateData($address->getData());
                            if ($addressErrors !== true) {
                                return $this->getJsonResponse(
                                    [
                                    'error' => 1,
                                    'message' => json_encode($addressErrors),
                                    ]
                                );
                            }
                        }
                    } else {
                        $addressForm->setEntity($address);
                        $addressData = ['firstname' => $newAddress->firstName,
                                'lastname' => $newAddress->lastName,
                                'company' => $newAddress->company,
                                'street' => [$newAddress->street[0],
                                             $newAddress->street[1],],
                                'city' => $newAddress->city,
                                'country_id' => $newAddress->country_id,
                                'region' => $newAddress->region,
                                'region_id' => $newAddress->region_id,
                                'postcode' => $newAddress->postcode,
                                'telephone' => $newAddress->telephone,
                                'fax' => $newAddress->fax,
                                'vat_id' => ''];
                        $addressErrors = $addressForm->validateData($addressData);
                        if ($addressErrors !== true) {
                            return $this->getJsonResponse(
                                ['error' => 1,
                                'message' => array_values($addressErrors),]
                            );
                        }
                        $addressForm->compactData($addressData);

                        $address->setCustomerAddressId(null);
                        $address->setSaveInAddressBook($saveInAddressBook);
                        $quote->setCustomerFirstname(
                            $newAddress->firstName
                        )->setCustomerLastname($newAddress->lastName);
                    }

                    if (in_array($checkoutMethod, ['register', 'guest'])) {
                        $quote->setCustomerEmail(trim($newAddress->emailAddress));
                        $address->setEmail(trim($newAddress->emailAddress));
                    }
                    if (!$address->getEmail() && $quote->getCustomerEmail()) {
                        $address->setEmail($quote->getCustomerEmail());
                    }

                    if (($validateRes = $address->validate()) !== true) {
                        $this->createLog('Address Data : ', [$address->getState(), $address->getCity()]);
                        $message = implode(', ', $validateRes);
                        return $this->getJsonResponse(
                            [
                            'error' => 1,
                            'message' => __($message),
                            ]
                        );
                    }
                    // $address->implodeStreetAddress();
                    if (true !== ($result = $this->_validateCustomerData(
                        $this->getRequest()->getPost()
                    ))
                    ) {
                        return $this->getJsonResponse($result);
                    }
                    if (!$quote->getCustomerId() && 'register' == $quote->getCheckoutMethod()) {
                        if ($this->_customerEmailExists(
                            $address->getEmail(),
                            $this->_storeManager
                                ->getStore()->getWebsiteId()
                        )
                        ) {
                            return $this->getJsonResponse(
                                [
                                'error' => 1,
                                'message' => __('This email already exist.'),
                                ]
                            );
                        }
                    }
                    if (!$quote->isVirtual()) {
                        $usingCase = isset($useForShipping) ? (int) $useForShipping : 0;
                        switch ($usingCase) {
                            case 0:
                                $shipping = $quote->getShippingAddress();
                                $shipping->setSameAsBilling(0);
                                $setStepDataShipping = 0;
                                break;
                            case 1:
                                $billing = clone $address;
                                $billing->unsAddressId()->unsAddressType();
                                $shipping = $quote->getShippingAddress();
                                $shippingMethod = $shipping->getShippingMethod();
                                $shipping->addData($billing->getData())
                                    ->setSameAsBilling(1)
                                    ->setSaveInAddressBook(0)
                                    ->setShippingMethod($shippingMethod)
                                    ->setCollectShippingRates(true);
                                $setStepDataShipping = 1;
                                break;
                        }
                    }
                    $quote->collectTotals()->save();
                    if (!$quote->isVirtual() && $setStepDataShipping) {
                        $quote->getShippingAddress()->setCollectShippingRates(true);
                    }
                } else {
                    return $this->getJsonResponse(
                        [
                            'error' => 1,
                            'message' => __('Invalid Billing data.'),
                        ]
                    );
                }

                /*
                 *
                 * Step 4 process starts here
                 *
                 */
                //$this->getRequest()->setParam("shippingData", $adDa);
                $shippingData = $this->getRequest()->getParam('shippingData');
                $shippingData = json_decode($shippingData);
                if (!$quote->isVirtual() && $useForShipping == 0) {
                    if (!empty($shippingData)) {
                        $same_as_billing = 0;
                        if ($shippingData->same_as_billing != '') {
                            $same_as_billing = $shippingData->same_as_billing;
                        }
                        $newAddress = '';
                        if ($shippingData->newAddress != '') {
                            if (!empty($shippingData->newAddress)) {
                                $newAddress = $shippingData->newAddress;
                            }
                        }

                        $addressId = '';
                        if ($shippingData->addressId != '') {
                            $addressId = $shippingData->addressId;
                        }

                        $saveInAddressBook = 0;
                        if (isset($shippingData->newAddress->save_in_address_book) && $shippingData->newAddress->save_in_address_book != '') {
                            $saveInAddressBook = $shippingData->newAddress->save_in_address_book;
                        }
                        $address = $quote->getShippingAddress();
                        $addressForm = $this->_objectManager
                            ->create("\Magento\Customer\Model\Form");
                        $addressForm->setFormCode('customer_address_edit')->setEntityType('customer_address');
                        if (is_numeric($addressId)) {
                            $customerAddress = $this->_objectManager
                                ->create("\Magento\Customer\Model\Address")
                                ->load($addressId)
                                ->getDataModel();
                            if ($customerAddress->getId()) {
                                if ($customerAddress->getCustomerId() != $quote->getCustomerId()) {
                                    return $this->getJsonResponse(
                                        [
                                        'error' => 1,
                                        'message' => __('Customer Address is not valid.'),
                                        ]
                                    );
                                }
                                $address->importCustomerAddressData($customerAddress)->setSaveInAddressBook(0);
                                $addressForm->setEntity($address);
                                $addressErrors = $addressForm->validateData($address->getData());
                                if ($addressErrors !== true) {
                                    return $this->getJsonResponse(
                                        [
                                        'error' => 1,
                                        'message' => $addressErrors,
                                        ]
                                    );
                                }
                            }
                        } elseif (!$same_as_billing) {
                            $addressForm->setEntity($address);
                            $addressData = [
                                    'firstname' => $newAddress->firstName,
                                    'lastname' => $newAddress->lastName,
                                    'company' => $newAddress->company,
                                    'street' => [
                                                        $newAddress->street[0],
                                                        $newAddress->street[1],
                                                    ],
                                    'city' => $newAddress->city,
                                    'country_id' => $newAddress->country_id,
                                    'region' => $newAddress->region,
                                    'region_id' => $newAddress->region_id,
                                    'postcode' => $newAddress->postcode,
                                    'telephone' => $newAddress->telephone,
                                    'fax' => $newAddress->fax,
                                    'vat_id' => '',
                                ];
                            $addressErrors = $addressForm->validateData($addressData);
                            if ($addressErrors !== true) {
                                return $this->getJsonResponse(
                                    [
                                    'error' => 1,
                                    'message' => $addressErrors,
                                    ]
                                );
                            }
                            $addressForm->compactData($addressData);
                            $address->setCustomerAddressId(null);

                            // Additional form data, not fetched by extractData (as it fetches only attributes)
                            $address->setSaveInAddressBook($saveInAddressBook);
                            $address->setSameAsBilling($same_as_billing);
                        }
                        //$address->implodeStreetAddress();
                        $address->setCollectShippingRates(true);
                        if (($validateRes = $address->validate()) !== true) {
                            $messagev = implode(', ', $validateRes);
                            return $this->getJsonResponse(
                                [
                                'error' => 1,
                                'message' => __($messagev),
                                ]
                            );
                        }
                        $quote->collectTotals()->save();
                    } else {
                        return $this->getJsonResponse(
                            [
                            'error' => 1,
                            'message' => __('Invalid Shipping data.'),
                            ]
                        );
                    }
                }
                if (!$quote->isVirtual()) {
                    $quote->getShippingAddress()->collectShippingRates()->save();
                    $_shippingRateGroups = $quote->getShippingAddress()->getGroupedAllShippingRates();
                    foreach ($_shippingRateGroups as $code => $_rates) {
                        $oneShipping = [];
                        $oneShipping['title'] = $this->_helperCatalog
                            ->stripTags(
                                $this->_helper->getConfigData('carriers/'.$code.'/title')
                            );
                        foreach ($_rates as $_rate) {
                            $oneMethod = [];
                            if ($_rate->getErrorMessage()) {
                                $onemethod['error'] = $_rate->getErrorMessage();
                            }
                            $oneMethod['code'] = $_rate->getCode();
                            $oneMethod['label'] = $_rate->getMethodTitle();
                            $oneMethod['price'] = $this->_helperCatalog
                                ->stripTags(
                                    $this->_priceHelper
                                        ->currency((float) $_rate->getPrice())
                                );
                            $oneShipping['method'][] = $oneMethod;
                        }
                        $returnArray['shippingMethods'][] = $oneShipping;
                    }
                }
                foreach ($this->_objectManager->create(
                    "\Magento\Payment\Helper\Data"
                )->getStoreMethods(
                    $storeId,
                    $quote
                ) as $_method) {
                    if ($_method->isAvailable(
                        $quote
                    )
                    ) {
                        $oneMethod = [];
                        $oneMethod['code'] = $_method->getCode();
                        $oneMethod['title'] = $_method->getTitle();
                        $oneMethod['extraInformation'] = '';
                        if (in_array(
                            $_method->getCode(),
                            ['paypal_standard', 'paypal_express']
                        )
                        ) {
                            if ($_method->getCode() == 'paypal_express') {
                                $oneMethod['extraInformation'] = __('You will be redirected to the PayPal website.');
                            } else {
                                $oneMethod['extraInformation'] =
                                __('You will be redirected to the PayPal website.');
                            }
                            $config = $this->_objectManager
                                ->create("\Magento\Paypal\Model\Config")
                                ->setMethod($_method->getCode());

                            $locale = $this->_objectManager
                                ->create("\Magento\Framework\Locale\ResolverInterface");
                                
                            $oneMethod['title'] = '';
                            $oneMethod['link'] = $config->getPaymentMarkWhatIsPaypalUrl($locale);
                            
                            $oneMethod['imageUrl'] = $config->getPaymentMarkImageUrl($locale->getLocale());
                            
                        } elseif (in_array(
                            $_method->getCode(),
                            ['paypal_express_bml']
                        )
                        ) {
                            $oneMethod['extraInformation'] =
                            __('You will be redirected to the PayPal website.');
                            $oneMethod['title'] = '';
                            $oneMethod['link'] =
                            'https://www.securecheckout.billmelater.com/paycapture-content/fetch?hash=AU826TU8&content=/bmlweb/ppwpsiw.html';
                            $oneMethod['imageUrl'] =
                            'https://www.paypalobjects.com/webstatic/en_US/i/buttons/ppc-acceptance-medium.png';
                        } elseif ($_method->getCode() == 'checkmo') {
                            if ($_method->getPayableTo()) {
                                $extraInformationPrefix = __('Make Check payable to:');
                            } else {
                                $extraInformationPrefix = __('Send Check to:');
                            }
                            $extraInformation = $this->_helper
                                ->getConfigData('payment/'.$_method->getCode().'/mailing_address');
                            if ($extraInformation == '') {
                                $extraInformation = ' xxxxxxx';
                            }
                            $oneMethod['extraInformation'] = $extraInformationPrefix.$extraInformation;
                        } elseif ($_method->getCode() == 'banktransfer') {
                            $extraInformation = $this->_helper
                                ->getConfigData('payment/'.$_method->getCode().'/instructions');
                            if ($extraInformation == '') {
                                $extraInformation = 'Bank Details are xxxxxxx';
                            }
                            $oneMethod['extraInformation'] = $extraInformation;
                        } elseif ($_method->getCode() == 'cashondelivery') {
                            $extraInformation = $this->_helper
                                ->getConfigData('payment/'.$_method->getCode().'/instructions');
                            if ($extraInformation == '') {
                                $extraInformation = 'Pay at the time of delivery';
                            }
                            $oneMethod['extraInformation'] = $extraInformation;
                        } elseif (in_array(
                            $_method->getCode(),
                            ['Custom_stripe', 'authorizenet']
                        )
                        ) {
                            $allowedCc = [];
                            $allowedCcTypesString = $_method->getConfigData('cctypes');
                            $allowedCcTypes = explode(',', $allowedCcTypesString);
                            $_types = $this->_objectManager
                                ->create("\Magento\Payment\Model\Source\Cctype")
                                ->toOptionArray();
                            $types = [];
                            foreach ($_types as $data) {
                                if (isset($data['value']) && isset($data['label'])) {
                                    $types[$data['value']] = $data['label'];
                                }
                            }

                            foreach ($allowedCcTypes as $value) {
                                $eachAllowedCc = [];
                                $eachAllowedCc['code'] = $value;
                                $eachAllowedCc['name'] = $types[$value];
                                $allowedCc[] = $eachAllowedCc;
                            }
                            $extraInformation = $allowedCc;
                            $oneMethod['extraInformation'] = $extraInformation;
                        }
                        $returnArray['paymentMethods'][] = $oneMethod;
                    }
                }

                /*
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                $returnArray['success'] = 1;

                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
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
