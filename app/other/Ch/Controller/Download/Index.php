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

namespace Custom\Chharo\Controller\Download;

use Magento\Framework\App\Action\Context;
use Custom\Chharo\Helper\Data as HelperData;
use Custom\Chharo\Helper\Catalog as HelperCatalog;
use Magento\Store\Model\App\Emulation;

/**
 * Chharo API CHeckout controller.
 */
class Index extends \Custom\Chharo\Controller\ApiController
{
    /**
     * $_customerFactory.
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        HelperData $helper,
        HelperCatalog $helperCatalog,
        Emulation $emulate,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->_customerFactory = $customerFactory;
        parent::__construct($context, $helper, $helperCatalog, $emulate);
    }

    /**
     * execute category list.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $hash = $this->getRequest()->getParam('hash');
        $linkFile = '';
        $fileName = '';
        $linkPurchasedItem = $this->_objectManager
        ->create("\Magento\Downloadable\Model\Link\Purchased\Item")
        ->load($hash, 'link_hash');
       
        $downloadsLeft = $linkPurchasedItem
        ->getNumberOfDownloadsBought() - $linkPurchasedItem->getNumberOfDownloadsUsed();

        $status = $linkPurchasedItem->getStatus();

        if ($status == \Magento\Downloadable\Model\Link\Purchased\Item::LINK_STATUS_AVAILABLE &&
             ($downloadsLeft || $linkPurchasedItem->getNumberOfDownloadsBought() == 0)
        ) {
            if ($linkPurchasedItem->getLinkType() == \Magento\Downloadable\Helper\Download::LINK_TYPE_FILE
            ) {
                $linkFile = $this->_objectManager
                ->create("\Magento\Downloadable\Helper\File")
                ->getFilePath(
                    $this->_objectManager
                    ->create("\Magento\Downloadable\Model\Link")
                    ->getBasePath(),
                    $linkPurchasedItem->getLinkFile()
                );
                $linkFile = $this->_helperCatalog->getBasePath("media")."/".$linkFile;
                $fileArray = explode(DS, $linkFile);
                $fileName = end($fileArray);
                $linkPurchasedItem->setNumberOfDownloadsUsed($linkPurchasedItem->getNumberOfDownloadsUsed() + 1);
                if ($linkPurchasedItem->getNumberOfDownloadsBought() != 0 &&
                    !($downloadsLeft - 1)
                ) {
                    $linkPurchasedItem
                        ->setStatus(\Magento\Downloadable\Model\Link\Purchased\Item::LINK_STATUS_EXPIRED);
                }
                $linkPurchasedItem->save();
            }
        }
        return $this->_objectManager->create("\Magento\Framework\App\Response\Http\FileFactory")->create(
            $fileName,
            @file_get_contents($linkFile)
        );
    }
}
