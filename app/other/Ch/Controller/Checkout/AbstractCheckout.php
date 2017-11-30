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

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Custom\Chharo\Helper\Data as HelperData;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\App\Emulation;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Payment\Transaction;

/**
 * Chharo API Catalog controller.
 */
abstract class AbstractCheckout extends \Custom\Chharo\Controller\ApiController
{
    /**
     * $_customerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * $_quoteFactory
     *
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * $_productFactory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * $_priceHelper
     *
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_priceHelper;

    /**
     * $_cart
     *
     * @var \Magento\Checkout\Model\CartFactory
     */
    protected $_cartFactory;

    /**
     * $_downloadableConfiguration
     *
     * @var \Magento\Downloadable\Helper\Catalog\Product\Configuration
     */
    protected $_downloadableConfiguration;

    /**
     * $_checkoutSession
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * $_orderFactory
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * $_customerSession
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * $_stockRegistry
     *
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $_stockRegistry;

    /**
     * $_coreRegistry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * $_customerRepository
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * $_country
     *
     * @var \Magento\Directory\Model\Country
     */
    protected $_country;

    /**
     * $_regionCollection
     *
     * @var \Magento\Directory\Model\Region
     */
    protected $_regionCollection;

    /**
     * $_storeManager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\DataObject\Copy
     */
    protected $_objectCopyService;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $_dataObjectHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;


    protected $_quote = null;

    /**
     * @var InvoiceSender
     */
    protected $_invoiceSender;

    /**
     * $_transactionBuilder.
     *
     * @var Transaction
     */
    protected $_transactionBuilder;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        HelperData $helper,
        \Custom\Chharo\Helper\Catalog $helperCatalog,
        Emulation $emulate,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Downloadable\Helper\Catalog\Product\Configuration $downloadableConfiguration,
        \Magento\Checkout\Model\CartFactory $cartFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Directory\Model\Country $country,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DataObject\Copy $objectCopyService,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Psr\Log\LoggerInterface $logger,
        InvoiceSender $invoiceSender,
        Transaction\BuilderInterface $transactionBuilder
    ) {
    
        $this->_customerFactory = $customerFactory;
        $this->_quoteFactory = $quoteFactory;
        $this->_productFactory = $productFactory;
        $this->_priceHelper = $priceHelper;
        $this->_cartFactory = $cartFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_customerSession = $customerSession;
        $this->_downloadableConfiguration = $downloadableConfiguration;
        $this->_stockRegistry =  $stockRegistry;
        $this->_coreRegistry = $coreRegistry;
        $this->_customerRepository = $customerRepository;
        $this->_country = $country;
        $this->_regionCollection = $regionCollection;
        $this->_storeManager = $storeManager;
        $this->_objectCopyService = $objectCopyService;
        $this->_dataObjectHelper = $dataObjectHelper;
        $this->_logger = $logger;
        $this->_transactionBuilder = $transactionBuilder;
        $this->_invoiceSender = $invoiceSender;
        parent::__construct($context, $helper, $helperCatalog, $emulate);
    }

    /**
     * Return array with customer Email Exists
     *
     * @param  $email define the email
     * @param  $website define website Id
     * @return array
     */
    protected function _customerEmailExists($email, $websiteId = null)
    {
        $customer = $this->_customerFactory->create();
        if ($websiteId) {
            $customer->setWebsiteId($websiteId);
        }
        $customer->loadByEmail($email);
        if ($customer->getId()) {
            return $customer;
        }
        return false;
    }

     /**
     * _validateCustomerData validate post data
     * @param  array $data
     * @return array|boolean
     */
    protected function _validateCustomerData($data)
    {
        $storeId = $data["storeId"];
        $customerData = [];
        $customer = null;
        $customerForm = $this->_objectManager
        ->create("\Magento\Customer\Model\Form")
        ->setFormCode("checkout_register");
        if (isset($data["customerId"])) {
            $customerId = $data["customerId"];
            $quoteCollection = $this->_quoteFactory->create()->getCollection();
            $quoteCollection->addFieldToFilter("customer_id", $customerId);
            $quoteCollection->addOrder("updated_at", "desc");
            $quote = $quoteCollection->getFirstItem();
        }
        if (isset($data["quoteId"])) {
            $quoteId = $data["quoteId"];
            $quote = $this->_quoteFactory->create()->setStoreId($storeId)
            ->load($quoteId);
        }
        if ($quote->getCustomerId()) {
            $customer = $quote->getCustomer();
            $customer = $this->_customerFactory
            ->create()
            ->load($customer->getId());
            $customerForm->setEntity($customer);
            $customerData = $customer->getData();
        } else {
            $customer = $this->_customerFactory->create();
            $customerForm->setEntity($customer);
            $newAddress = "";
            $billingData = $data["billingData"];
            $billingData = json_decode($billingData);

            if (isset($billingData->newAddress)) {
                if (!empty($billingData->newAddress)) {
                    $newAddress = $billingData->newAddress;
                }
            }
            $customerData = [
                "firstname" => $newAddress->firstName,
                "lastname" => $newAddress->lastName,
                "email" => trim($newAddress->emailAddress)
            ];
        }
        $this->createLog("customer data :", $customerData);
        $customerErrors = true;

        if ($customerErrors !== true) {
            return ["error" => 1, "message" => implode(", ", $customerErrors)];
        }
        if ($quote->getCustomerId()) {
            return true;
        }
        
        if ($quote->getCheckoutMethod() == "register") {
            $customerForm->compactData($customerData);
            $customer->setPassword($data["password"]);
            $customer->setConfirmation($data["confirmPassword"]);
            $customer->setPasswordConfirmation($data["confirmPassword"]);
            $result = $customer->validate();
            if (true !== $result && is_array($result)) {
                return ["error"   => -1, "message" => implode(", ", $result)];
            }
        }
        

        
        if ($quote->getCheckoutMethod() == "register") {
            $quote->setPasswordHash($customer->encryptPassword($customer->getPassword()));
            $quote->setCustomer($customer);
        }
        $quote->getBillingAddress()->setEmail($customer->getEmail());
       

        $this->_objectManager
        ->create("\Magento\Framework\DataObject\Copy")
        ->copyFieldsetToTarget("customer_account", "to_quote", $customer, $quote);
        return true;
    }
}
