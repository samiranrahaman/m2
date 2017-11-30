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

/**
 * Chharo API Customer controller.
 */
class ReOrder extends \Custom\Chharo\Controller\ApiController
{
    /**
     * $_customerFactory.
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * $_stockRegistry.
     *
     * @var \Magento\CatalogInventory\Api\StockRegistry
     */
    protected $_stockRegistry;

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
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_stockRegistry = $stockRegistry;
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
            $incrementId = $this->getRequest()->getPost('incrementId');
            $storeId = $this->getRequest()->getPost('storeId');
            $customerId = $this->getRequest()->getPost('customerId');
            $returnArray = [];
            if ($storeId == '') {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                /**
                 * $initialEnvironmentInfo store emulation start.
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $returnArray['error'] = 0;
                $returnArray['message'] = __('Product(s) Has been Added to cart.');

                $_order = $this->_objectManager
                    ->create("\Magento\Sales\Model\Order")
                    ->loadByIncrementId($incrementId);

                if ($_order->getCustomerId() != $customerId) {
                    $returnArray['success'] = 0;
                    $returnArray['message'] = __('Invalid Order.');

                    return $this->getJsonResponse($returnArray);
                }

                if (!$_order || !$_order->getId()) {
                    $returnArray['success'] = 0;
                    $returnArray['message'] = __('Invalid Order.');

                    return $this->getJsonResponse($returnArray);
                }

                $cart = $this->_objectManager
                ->create("\Magento\Checkout\Model\CartFactory")->create();
                $quoteCollection = $this->_objectManager->create("\Magento\Quote\Model\QuoteFactory")->create()->getCollection();
                $quoteCollection->addFieldToFilter('customer_id', $customerId);
                $quoteCollection->addOrder('updated_at', 'desc');
                $quote = $quoteCollection->getFirstItem();
                $quoteId = $quote->getId();
                $quote = $this->_objectManager->create("\Magento\Quote\Model\QuoteFactory")->create()
                    ->setStoreId($storeId)->load($quoteId);
                $cart->setQuote($quote);
               
                $items = $_order->getItemsCollection();
                foreach ($items as $item) {
                    $cart->addOrderItem($item);
                }

                $cart->save();
                /*
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                $returnArray['success'] = 1;

                return $this->getJsonResponse($returnArray);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->createLog('Chharo Exception log for class: '.get_class($this).' : '.$e->getMessage(), (array) $e->getTrace());
                $returnArray['success'] = 0;
                $returnArray['message'] = __($e->getMessage());

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
}
