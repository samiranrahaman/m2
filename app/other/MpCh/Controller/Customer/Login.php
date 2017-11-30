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

namespace Custom\MpChharo\Controller\Customer;

use Magento\Framework\App\Action\Context;
use Custom\Chharo\Helper\Data as HelperData;
use Custom\Chharo\Helper\Catalog as HelperCatalog;
use Magento\Store\Model\App\Emulation;

/**
 * MpChharo API Customer controller.
 */
class Login extends \Custom\Chharo\Controller\ApiController
{
    /**
     * $_customerFactory.
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $_encryptor;

    /**
     * $_baseDir.
     *
     * @var String
     */
    protected $_baseDir;

    /**
     * $_dir.
     *
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $_dir;

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
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Filesystem\DirectoryList $dir
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_encryptor = $encryptor;
        $this->_dir = $dir;
        parent::__construct($context, $helper, $helperCatalog, $emulate);

        $this->_baseDir = $this->_dir
            ->getPath('media');
    }

    /**
     * execute category list.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $username = $this->getRequest()->getPost('username');
            $password = $this->getRequest()->getPost('password');
            $storeId = $this->getRequest()->getPost('storeId');
            $websiteId = $this->getRequest()->getPost('websiteId');
            $token = $this->getRequest()->getPost('token');
            if ($storeId == '') {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                $error = 0;
                $customerModel = $this->_customerFactory->create();
                $customer = $customerModel->setWebsiteId($websiteId)->loadByEmail($username);

                if ($customer->getId() > 0) {
                    $customerId = $customer->getId();
                    $customer = $customerModel->setWebsiteId($websiteId);
                    if ($customerModel->getConfirmation() &&
                         $customerModel->isConfirmationRequired()
                     ) {
                        $returnArray['status'] = 'false';
                        $returnArray['customerName'] = '';
                        $returnArray['customerEmail'] = '';
                        $returnArray['customerId'] = '';
                        $returnArray['cartCount'] = 0;
                        $returnArray['isSeller'] = 0;
                        $returnArray['message'] = __('This account is not confirmed.');
                        $returnArray['success'] = 1;

                        return $this->getJsonResponse($returnArray);
                    }

                    $hash = $customerModel->getPasswordHash();
                    $validatePassword = 0;
                    if (!$hash) {
                        $validatePassword = false;
                    }
                    $validatePassword = $this->_encryptor->validateHash($password, $hash);
                    if (!$validatePassword) {
                        $returnArray['status'] = 'false';
                        $returnArray['customerName'] = '';
                        $returnArray['customerEmail'] = '';
                        $returnArray['customerId'] = '';
                        $returnArray['cartCount'] = 0;
                        $returnArray['isSeller'] = 0;
                        $returnArray['message'] = __('Invalid login or password.');
                        $returnArray['success'] = 1;

                        return $this->getJsonResponse($returnArray);
                    }
                    $adminEmail = $this->_helper->getConfigData("mpchharo/admin/email");
                    $isAdmin = 0;
                    if($adminEmail && $adminEmail == $customer->getEmail()) {
                        $isAdmin = 1;
                    } 
                    $returnArray = [];
                    $returnArray['status'] = 'true';
                    $returnArray['customerName'] = $customer->getFirstname().' '.$customer->getLastname();
                    $returnArray['customerEmail'] = $customer->getEmail();
                    $returnArray['customerId'] = $customer->getId();
                    $returnArray['isAdmin'] = $isAdmin;

                    $quoteCollection = $this->_objectManager
                    ->create("\Magento\Quote\Model\Quote")
                    ->getCollection();

                    //update token for notification
                    $this->_objectManager->get("\Custom\Chharo\Helper\Token")
                    ->setAndroidToken($customer->getId(), $token);

                    $quoteCollection->addFieldToFilter(
                        'customer_id',
                        $customer->getId()
                    );
                    $quoteCollection->addOrder(
                        'updated_at',
                        'desc'
                    );
                    $quote = $quoteCollection->getFirstItem();
                    $returnArray['cartCount'] = $quote->getItemsQty() * 1;
                    $width = $this->getRequest()->getPost('width');
                    if ($width != '') {
                        $width = $this->getRequest()->getPost('width');
                    } else {
                        $width = 1000;
                    }
                    $height = $width / 2;

                    $collection = $this->_objectManager
                    ->create("\Custom\Chharo\Model\UserImage")
                    ->getCollection()
                    ->addFieldToFilter(
                        'customer_id',
                        $customer->getId()
                    );
                    $returnArray['customerBannerImage'] = '';
                    $returnArray['customerProfileImage'] = '';

                    if ($collection->getSize() > 0) {
                        foreach ($collection as $value) {
                            if ($value->getBanner() != '') {
                                $basePath = $this->_baseDir
                                .DS.
                                'chharo'
                                .DS.
                                'customerpicture'
                                .DS.
                                $customerId
                                .DS.
                                $value->getBanner();

                                $newUrl = '';
                                if (file_exists($basePath)) {
                                    $newPath = $this->_baseDir
                                    .DS.
                                    'chharo'
                                    .DS.
                                    'customerpicture'
                                    .DS.
                                    $customerId
                                    .DS.
                                    $width
                                    .'x'.
                                    $height
                                    .DS.
                                    $value->getBanner();

                                    $newUrl = $this->_helper->getUrl('media')
                                    .'chharo'
                                    .DS
                                    .'customerpicture'
                                    .DS.
                                    $customerId
                                    .DS.
                                    $width
                                    .'x'.
                                    $height
                                    .DS.
                                    $value->getBanner();

                                    if (!file_exists($newPath)) {
                                        $imageObj = $this->_helperCatalog->imageUpload($basePath, $newPath, $width, $height);
                                    }
                                }
                                $returnArray['customerBannerImage'] = $newUrl;
                            }

                            if ($value->getProfile() != '') {
                                $basePath = $this->_baseDir
                                .DS.
                                'chharo'
                                .DS
                                .'customerpicture'
                                .DS.
                                $customerId
                                .DS.
                                $value->getProfile();

                                $newUrl = '';
                                if (file_exists($basePath)) {
                                    $newPath = $this->_baseDir
                                    .DS.
                                    'chharo'
                                    .DS
                                    .'customerpicture'
                                    .DS.
                                    $customerId
                                    .DS.
                                    '100x100'
                                    .DS.
                                    $value->getProfile();

                                    $newUrl = $this->_helper->getUrl('media')
                                    .'chharo'
                                    .DS
                                    .'customerpicture'
                                    .DS.
                                    $customerId
                                    .DS.
                                    '100x100'
                                    .DS.
                                    $value->getProfile();

                                    if (!file_exists($newPath)) {
                                        $imageObj = $this->_helperCatalog->imageUpload($basePath, $newPath, 70, 70);
                                    }
                                }
                                $returnArray['customerProfileImage'] = $newUrl;
                            }
                        }
                    }

                    $quoteId = $this->getRequest()->getPost('quoteId');
                    if ($quoteId != '') {
                        $guestQuote = $this->_objectManager
                        ->create("\Magento\Quote\Model\Quote")
                        ->setStoreId($storeId)
                        ->load($this->getRequest()->getPost('quoteId'));

                        $quoteCollection = $this->_objectManager
                        ->create("\Magento\Quote\Model\Quote")
                        ->getCollection();
                        $quoteCollection->addFieldToFilter('customer_id', $customer->getId());

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

                    $returnArray['success'] = 1;
                    $partnerId = $customer->getId();
                    $partnerData = 0;
                    $collection =
                    $this->_objectManager
                    ->create("\Custom\Marketplace\Model\Seller")
                    ->getCollection()
                    ->addFieldToFilter('seller_id', $partnerId);

                    foreach ($collection as $record) {
                        $partnerData = $record->getIsSeller();
                        $returnArray['profileUrl'] = $record->getShopUrl();
                    }

                    if ($partnerData == 1) {
                        $returnArray['isSeller'] = 1;
                    } else {
                        $returnArray['isSeller'] = 0;
                    }

                    return $this->getJsonResponse($returnArray);
                } else {
                    $error = 1;
                }

                if ($error == 1) {
                    $returnArray = [];
                    $returnArray['status'] = 'false';
                    $returnArray['customerName'] = '';
                    $returnArray['customerEmail'] = '';
                    $returnArray['customerId'] = '';
                    $returnArray['cartCount'] = 0;
                    $returnArray['isSeller'] = 0;
                    $returnArray['message'] = __('Invalid login or password.');
                    $returnArray['success'] = 1;

                    return $this->getJsonResponse($returnArray);
                }
            } catch (\Exception $e) {
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
