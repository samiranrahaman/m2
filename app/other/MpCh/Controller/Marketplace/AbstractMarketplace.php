<?php
/**
 * Custom Software.
 *
 * @category  Custom
 *
 * @author    Custom
 * @copyright Copyright (c) 2010-2017 Custom Software Private Limited (https://Custom.com)
 * @license   https://store.Custom.com/license.html
 */
namespace Custom\MpChharo\Controller\Marketplace;

use Magento\Framework\App\Action\Context;
use Custom\Chharo\Helper\Data as HelperData;
use Magento\Store\Model\App\Emulation;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Payment\Transaction;

/**
 * MpChharo API Marketplace controller.
 */
abstract class AbstractMarketplace extends \Custom\Chharo\Controller\ApiController
{
    /**
     * $_customerFactory.
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * $_quoteFactory.
     *
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * $_productFactory.
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * $_priceHelper.
     *
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_priceHelper;

    /**
     * $_cart.
     *
     * @var \Magento\Checkout\Model\CartFactory
     */
    protected $_cartFactory;

    /**
     * $_downloadableConfiguration.
     *
     * @var \Magento\Downloadable\Helper\Catalog\Product\Configuration
     */
    protected $_downloadableConfiguration;

    /**
     * $_checkoutSession.
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * $_orderFactory.
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * $_customerSession.
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * $_stockRegistry.
     *
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $_stockRegistry;

    /**
     * $_coreRegistry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * $_customerRepository.
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * $_country.
     *
     * @var \Magento\Directory\Model\Country
     */
    protected $_country;

    /**
     * $_regionCollection.
     *
     * @var \Magento\Directory\Model\Region
     */
    protected $_regionCollection;

    /**
     * $_storeManager.
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
     * $_marketplaceHelper.
     *
     * @var Custom\Marketplace\Helper\Data
     */
    protected $_marketplaceHelper;

    /**
     *
     */
    protected $_mediaDir;

    protected $_viewFilePath;

    public $_dateTime;

    public $_priceFormat;

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
        Transaction\BuilderInterface $transactionBuilder,
        \Custom\Marketplace\Helper\Data $marketplaceHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection
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
        $this->_stockRegistry = $stockRegistry;
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
        $this->_marketplaceHelper = $marketplaceHelper;
        $this->_resourceConnection = $resourceConnection;
        parent::__construct($context, $helper, $helperCatalog, $emulate);

        $this->_mediaDir = $this->_objectManager->get('Magento\Framework\Filesystem\DirectoryList')->getPath('media');
        $this->_viewFilePath = $this->_objectManager->get('Magento\Framework\Module\Dir\Reader')->getModuleDir('view', 'Custom_Marketplace').DS.'frontend'.DS.'web';
        $this->_dateTime = $this->_objectManager
        ->create("\Magento\Framework\Stdlib\DateTime");
        $this->_priceFormat = $this->_objectManager
        ->create('\Magento\Framework\Pricing\Helper\Data');
    }

    protected function _getItemQtys($order, $items)
    {
        $data = [];
        $subtotal = 0;
        $baseSubtotal = 0;
        foreach ($order->getAllItems() as $item) {
            if (in_array($item->getItemId(), $items)) {
                $data[$item->getItemId()] = intval($item->getQtyOrdered() - $item->getQtyInvoiced());

                $_item = $item;

                // for bundle product
                $bundleitems = array_merge([$_item], $_item->getChildrenItems());

                if ($_item->getParentItem()) {
                    continue;
                }

                if ($_item->getProductType() == 'bundle') {
                    foreach ($bundleitems as $_bundleitem) {
                        if ($_bundleitem->getParentItem()) {
                            $data[$_bundleitem->getItemId()] = intval(
                                $_bundleitem->getQtyOrdered() - $item->getQtyInvoiced()
                            );
                        }
                    }
                }
                $subtotal += $_item->getRowTotal();
                $baseSubtotal += $_item->getBaseRowTotal();
            } else {
                if (!$item->getParentItemId()) {
                    $data[$item->getItemId()] = 0;
                }
            }
        }

        return ['data' => $data,'subtotal' => $subtotal,'baseSubtotal' => $baseSubtotal];
    }

    /**
     * Initialize invoice model instance.
     *
     * @return \Magento\Sales\Api\InvoiceRepositoryInterface|false
     */
    protected function _initInvoice($invoiceId, $orderId, $sellerId)
    {
        $invoice = false;
        if (!$invoiceId) {
            return false;
        }
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $this->_objectManager->create(
            'Magento\Sales\Api\InvoiceRepositoryInterface'
        )->get($invoiceId);
        if (!$invoice) {
            return false;
        }
        try {
            $tracking = null;
            $marketplaceOrder = $this->_objectManager->create(
                'Custom\Marketplace\Model\OrdersFactory'
            )->create();
            $model = $marketplaceOrder
                ->getCollection()
                ->addFieldToFilter(
                    'seller_id',
                    $sellerId
                )
                ->addFieldToFilter(
                    'order_id',
                    $orderId
                );
            foreach ($model as $tracking) {
                $marketplaceOrder = $tracking;
            }
            $tracking = $marketplaceOrder;
            if (count($tracking)) {
                if ($tracking->getInvoiceId() == $invoiceId) {
                    if (!$invoiceId) {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return $invoice;
    }

    /**
     * Initialize invoice model instance.
     *
     * @return \Magento\Sales\Api\InvoiceRepositoryInterface|false
     */
    protected function _initCreditmemo($creditmemoId, $orderId, $sellerId)
    {
        $creditmemo = false;

        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $this->_objectManager->create('Magento\Sales\Api\CreditmemoRepositoryInterface')
        ->get($creditmemoId);
        if (!$creditmemo) {
            return false;
        }
        try {
            $tracking = null;
            $marketplaceOrder = $this->_objectManager->create(
                'Custom\Marketplace\Model\OrdersFactory'
            )->create();
            $model = $marketplaceOrder
                ->getCollection()
                ->addFieldToFilter(
                    'seller_id',
                    $sellerId
                )
                ->addFieldToFilter(
                    'order_id',
                    $orderId
                );
            foreach ($model as $tracking) {
                $marketplaceOrder = $tracking;
            }
            $tracking = $marketplaceOrder;

            if (count($tracking)) {
                $creditmemoArr = explode(',', $tracking->getCreditmemoId());
                if (in_array($creditmemoId, $creditmemoArr)) {
                    if (!$creditmemoId) {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return $creditmemo;
    }
}
