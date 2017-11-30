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

namespace Custom\Chharo\Controller\Checkout;

/**
 * Chharo API Checkout controller.
 */
class GetStepOnenTwoData extends AbstractCheckout
{


    /**
     * execute get data from checkout process
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $storeId = $this->getRequest()->getPost("storeId");
            $sessionId = $this->getRequest()->getPost("sessionId");
            $returnArray = [];

            if ($storeId == "") {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                /**
                 * $initialEnvironmentInfo store emulation start
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $addressIds = [];
                $customerId = $this->getRequest()->getPost("customerId");
                if ($customerId != "") {
                    $customerId = $this->getRequest()->getPost("customerId");
                    $customer = $this->_customerFactory
                        ->create()
                        ->load($customerId);
                    //get cutomer primary billing address
                    $address = $customer->getPrimaryBillingAddress();
                    if ($address instanceof \Magento\Framework\DataObject) {
                        $tempbillingAddress = [];
                        $tempbillingAddress["value"] = $address->getFirstname()." ".$address->getLastname()." ";
                        foreach ($address->getStreet() as $street) {
                            $tempbillingAddress["value"] .= $street.", ";
                        }
                        $tempbillingAddress["value"] .= $address->getCity()
                        .", ".$address->getRegion()
                        .", ".$address->getPostcode()
                        ." ".$this->_country->load(
                            $address->getCountryId()
                        )->getName();
                        $tempbillingAddress["id"] = $address->getId();
                        if (!in_array($address->getId(), $addressIds)) {
                            $addressIds[] = $address->getId();
                            $returnArray["address"][] = $tempbillingAddress;
                        }
                    }
                    //get customer primary shipping address
                    $address = $customer->getPrimaryShippingAddress();
                    if ($address instanceof \Magento\Framework\DataObject) {
                        $tempshippingAddress = [];
                        $tempshippingAddress["value"] = $address->getFirstname()." ".$address->getLastname()." ";
                        foreach ($address->getStreet() as $street) {
                            $tempshippingAddress["value"] .= $street.", ";
                        }
                        $tempshippingAddress["value"] .= $address->getCity()
                        .", ".$address->getRegion()
                        .", ".$address->getPostcode()
                        ." ".$this->_country->load(
                            $address->getCountryId()
                        )->getName();
                        $tempshippingAddress["id"] = $address->getId();
                        if (!in_array($address->getId(), $addressIds)) {
                            $addressIds[] = $address->getId();
                            $returnArray["address"][] = $tempshippingAddress;
                        }
                    }

                    //get all additional addresses
                    $additionalAddress = $customer->getAdditionalAddresses();
                    foreach ($additionalAddress as $key => $eachAdditionalAddress) {
                        if ($eachAdditionalAddress instanceof \Magento\Framework\DataObject) {
                            $eachAdditionalAddressArray = [];
                            $eachAdditionalAddressArray["value"] =
                            $eachAdditionalAddress->getFirstname()
                            ." ".$eachAdditionalAddress->getLastname()." ";
                            foreach ($eachAdditionalAddress->getStreet() as $street) {
                                $eachAdditionalAddressArray["value"] .= $street.", ";
                            }
                            $eachAdditionalAddressArray["value"] .=
                            $eachAdditionalAddress->getCity()
                            .", ".$eachAdditionalAddress->getRegion()
                            .", ".$eachAdditionalAddress->getPostcode()
                            ." ".$this->_country->load(
                                $eachAdditionalAddress->getCountryId()
                            )->getName()." ";
                            $eachAdditionalAddressArray["id"] = $eachAdditionalAddress->getId();
                            $returnArray["address"][] = $eachAdditionalAddressArray;
                        }
                    }
                } else {
                    //if no address is set by the customer
                    $returnArray["address"][] = [];
                }

                //create country and its region list
                $countryCollection = $this->_country->getResourceCollection()->loadByStore()->toOptionArray(true);
                unset($countryCollection[0]);
                foreach ($countryCollection as $country) {
                    $eachCountry = [];
                    $eachCountry["country_id"] = $country["value"];
                    $eachCountry["name"] = $country["label"];
                    $regionCollection = $this->_regionCollection->create()
                        ->addCountryFilter($eachCountry["country_id"])
                        ->toOptionArray();

                    if (count($regionCollection) > 0) {
                        $eachCountry["states"] = $regionCollection;
                    }
                    $returnArray["countryData"][] = $eachCountry;
                }
                if ($customerId != "") {
                    $quoteCollection = $this->_quoteFactory->create()->getCollection();
                    $quoteCollection->addFieldToFilter("customer_id", $customerId);
                    $quoteCollection->addOrder("updated_at", "desc");
                    $quote = $quoteCollection->getFirstItem();
                }
                $quoteId = $this->getRequest()->getPost("quoteId");
                if ($quoteId != "") {
                    $quote = $this->_quoteFactory
                        ->create()
                        ->setStoreId($storeId)->load($quoteId);
                }
                $returnArray["isVirtual"] = $quote->isVirtual();
               
                /**
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
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
