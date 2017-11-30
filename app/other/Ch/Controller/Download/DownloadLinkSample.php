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

namespace Custom\Chharo\Controller\Download;

use Magento\Framework\App\Action\Context;
use Custom\Chharo\Helper\Data as HelperData;
use Custom\Chharo\Helper\Catalog as HelperCatalog;
use Magento\Store\Model\App\Emulation;

/**
 * Chharo API CHeckout controller.
 */
class DownloadLinkSample extends \Custom\Chharo\Controller\ApiController
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
        $this->createLog("Chharo download link log for class: ".get_class($this)." : ", (array)$this->getRequest()->getParams());
        $linkId = $this->getRequest()->getParam('linkId');
        $link = $this->_objectManager
            ->create("\Magento\Downloadable\Model\Link")
            ->load($linkId);

        $sampleLinkFilePath = $this->_objectManager
            ->create("\Magento\Downloadable\Helper\File")
            ->getFilePath(
                $this->_objectManager
                    ->create("\Magento\Downloadable\Model\Link")
                    ->getBaseSamplePath(),
                $link->getSampleFile()
            );
        $sampleLinkFilePath = $this->_helperCatalog->getBasePath().'/'.$sampleLinkFilePath;
        $fileArray = explode(DS, $sampleLinkFilePath);
        $fileName = end($fileArray);
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.android.package-archive');
        header('Content-Disposition: attachment; filename='.$fileName);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: '.filesize($sampleLinkFilePath));
        ob_clean();
        flush();
        readfile($sampleLinkFilePath);
    }
}
