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
use Magento\Framework\Exception\LocalizedException;

/**
 * Chharo API Customer controller.
 */
class WishlistAddToCart extends \Custom\Chharo\Controller\ApiController
{
    /**
     * $_customerFactory.
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    protected $_quantityProcessor;

    /**
     * $_wishlistProvider.
     *
     * @var \Magento\Wishlist\Controller\WishlistProvider
     */
    protected $_wishlistProvider;

    /**
     * $_cart.
     *
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

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
        \Magento\Wishlist\Model\LocaleQuantityProcessor $quantityProcessor,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Wishlist\Model\Wishlist $wishlistProvider
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_quantityProcessor = $quantityProcessor;
        $this->_cart = $cart;
        $this->_wishlistProvider = $wishlistProvider;
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
            $qty = $this->getRequest()->getPost('qty');
            $customerId = $this->getRequest()->getPost('customerId');
            $productId = $this->getRequest()->getPost('productId');
            $storeId = $this->getRequest()->getPost('storeId');
            $itemId = (int) $this->getRequest()->getPost('itemId');
            if ($storeId == '') {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                /**
                 * $initialEnvironmentInfo store emulation start.
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                /*
                 * get wish list item model
                 */
                $item = $this->_objectManager
                    ->create("\Magento\Wishlist\Model\Item")
                    ->load($itemId);

                if (!$item || !$item->getId()) {
                    throw new LocalizedException(__('Item id is invalid'));
                }
                /*
                 * load wishlist model
                 */
                $wishlist = $this->_wishlistProvider->load($item->getWishlistId());
                if ($wishlist->getCustomerId() != $customerId) {
                    throw new LocalizedException(__('Invalid data.'));
                }

                $customer = $this->_customerFactory
                    ->create()
                    ->load($customerId);

                $this->_objectManager
                    ->get("\Magento\Customer\Model\Session")
                    ->setCustomer($customer);

                /*
                 * check quantity
                 */
                if ($qty == '' || $qty == 0) {
                    $qty = 1;
                }

                $qty = $this->_quantityProcessor->process($qty);
                if ($qty) {
                    $item->setQty($qty);
                }

                /*
                 * get item options collection
                 */
                $options = $this->_objectManager
                    ->create("\Magento\Wishlist\Model\Item\Option")
                    ->getCollection()
                    ->addItemFilter([$itemId]);

                $item->setOptions($options->getOptionsByItem($itemId));

                /*
                 * create buy request
                 */
                $buyRequest = $this->_objectManager->create("\Magento\Catalog\Helper\Product")
                    ->addParamsToBuyRequest(
                        ['item' => $itemId, 'qty' => [$itemId => $qty]],
                        ['current_config' => $item->getBuyRequest()]
                    );

                $item->mergeBuyRequest($buyRequest);

                $state = $item->addToCart($this->_cart, false);
                $this->_cart->save()->getQuote()->collectTotals();
                $returnArray['cartCount'] = $this->_cart->getQuote()->getItemsQty();
                $item->delete();
                $wishlist->save();

                $this->_objectManager
                    ->create("\Magento\Wishlist\Helper\Data")->calculate();
                /*
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                $returnArray['success'] = 1;
                $returnArray['message'] = __('Product(s) has successfully moved to cart.');

                return $this->getJsonResponse($returnArray);
            } catch (\Magento\Catalog\Model\Product\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray = [];
                $returnArray['success'] = 0;
                $returnArray['message'] = 'This product(s) is out of stock.';

                return $this->getJsonResponse($returnArray);
            } catch (LocalizedException $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray = [];
                $returnArray['success'] = 0;
                $returnArray['message'] = $e->getMessage();

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
