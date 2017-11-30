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

namespace Custom\Chharo\Controller\Customer;

use Magento\Framework\App\Action\Context;
use Custom\Chharo\Helper\Data as HelperData;
use Custom\Chharo\Helper\Catalog as HelperCatalog;
use Magento\Store\Model\App\Emulation;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Customer\Api\AccountManagementInterface;

/**
 * Chharo API Customer controller.
 */
class CreatePost extends \Custom\Chharo\Controller\ApiController
{
    /**
     * $_customerFactory.
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
 * @var AccountManagementInterface 
*/
    protected $_accountManagement;

    /**
     * $_dataObjectHelper.
     *
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $_dataObjectHelper;

    protected $_customerExtractor;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        HelperData $helper,
        HelperCatalog $helperCatalog,
        Emulation $emulate,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        AccountManagementInterface $accountManagement,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        CustomerExtractor $customerExtractor
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_accountManagement = $accountManagement;
        $this->_dataObjectHelper = $dataObjectHelper;
        $this->_customerExtractor = $customerExtractor;
        parent::__construct($context, $helper, $helperCatalog, $emulate);
    }

    /**
     * execute category list.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $returnArray = [];
            $firstName = $this->getRequest()->getPost('firstName');
            $lastName = $this->getRequest()->getPost('lastName');
			
			$link = fopen("d:\atest.txt","a+");
			fwrite($link,"hi".$firstName);
			fclose($link);
			
            $emailAddr = $this->getRequest()->getPost('emailAddr');
            $password = $this->getRequest()->getPost('password');
            $websiteId = $this->getRequest()->getPost('websiteId');
            $storeId = $this->getRequest()->getPost('storeId');
            $quoteId = $this->getRequest()->getPost('quoteId');
            $isSocial = $this->getRequest()->getPost('isSocial');
            $token = $this->getRequest()->getPost('token');
            $telephone = $this->getRequest()->getPost('telephone');
            if ($storeId == '') {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                /**
                 * $initialEnvironmentInfo store emulation start.
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $emailValidator = new \Zend\Validator\EmailAddress();

                if (!$emailValidator->isValid($emailAddr)) {
                    $returnArray['status'] = 'false';
                    $returnArray['message'] = __('Invalid email address.');
                    $returnArray['success'] = 0;

                    return $this->getJsonResponse($returnArray);
                }
                $customerCheck = $this->_customerFactory
                    ->create()
                    ->setWebsiteId($websiteId)
                    ->loadByEmail($emailAddr);

                if ($customerCheck->getId() > 0 && $isSocial == 'true') {
                    $returnArray['status'] = 'true';
                    $returnArray['customerId'] = $customerCheck->getId();
                    $returnArray['customerName'] = $customerCheck->getName();
                    $returnArray['customerEmail'] = $customerCheck->getEmail();
                    $returnArray['message'] = __('Customer exists please login');
                    $returnArray['success'] = 1;

                    return $this->getJsonResponse($returnArray);
                }

                if ($customerCheck->getId() > 0) {
                    $returnArray['status'] = 'false';
                    $returnArray['message'] = __('There is already an account with this email address.');
                    $returnArray['success'] = 0;

                    return $this->getJsonResponse($returnArray);
                }
                $customer = $this->_customerFactory->create();

                $customerData = [
                    'email' => $emailAddr,
                    'firstname' => $firstName,
                    'lastname' => $lastName,
                    'password' => $password,
                    'website_id' => $websiteId,
					'telephone' => $telephone,
                    'store_id' => $storeId,
                    'group_id' => $this->_helper->getConfigData(
                        \Magento\Customer\Model\GroupManagement::XML_PATH_DEFAULT_ID
                    ),

                ];
                /*
                 * set customer create data in request
                 */
                $this->getRequest()->setParams($customerData);

                /*
                 * create account
                 */
                $customerObject = $this->_customerExtractor
                    ->extract('customer_account_create', $this->_request);

                $customer = $this->_accountManagement
                    ->createAccount($customerObject, $password, '');
                $customerId = $customer->getId();
                //update token for notification
                    $this->_objectManager->get("\Custom\Chharo\Helper\Token")
                        ->setAndroidToken($customerId, $token);
                $isChecked = $this->getRequest()->getPost('isChecked');
                if ($isChecked != '') {
                    $this->_customerFactory->create()->load($customerId)->setIsSubscribed(1)->save();
                }
                $quoteId = $this->getRequest()->getPost('quoteId');
                if ($quoteId != '') {
                    $guestQuote = $this->_objectManager
                        ->create("\Magento\Quote\Model\Quote")
                        ->setStoreId($storeId)
                        ->load($quoteId);

                    $quoteCollection = $this->_objectManager
                        ->create("\Magento\Quote\Model\Quote")
                        ->getCollection();
                    $quoteCollection->
                    addFieldToFilter('customer_id', $customerId);

                    $quoteCollection->addOrder('updated_at', 'desc');

                    $customerQuote = $quoteCollection->getFirstItem();
                    if ($customerQuote->getId() > 0) {
                        $customerQuote->merge($guestQuote);
                        $customerQuote->collectTotals()->save();
                    } else {
                        $guestQuote->assignCustomer($customer);
                        $guestQuote->setCustomer($customer);
                        $guestQuote->getShippingAddress()->setCollectShippingRates(true);
                        $guestQuote->collectTotals()->save();
                    }
                }
                $returnArray['status'] = 'true';
                $returnArray['customerId'] = $customerId;
                $returnArray['customerName'] = $firstName.' '.$lastName;
                $returnArray['customerEmail'] = $emailAddr;
                $returnArray['message'] = __('Your Account has been successfully created');

                /*
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                $returnArray['success'] = 1;

                return $this->getJsonResponse($returnArray);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray['success'] = 0;
                $returnArray['message'] = __($e->getMessage());

                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray['success'] = 0;
                $returnArray['message'] = __('Invalid request');

                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray['success'] = 0;
            $returnArray['message'] = __('Invalid Request.');

            return $this->getJsonResponse($returnArray);
        }
    }
}
