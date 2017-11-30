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
class DownloadSample extends \Custom\Chharo\Controller\ApiController
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
        $this->createLog("Chharo download sample log for class: ".get_class($this)." : ", (array)$this->getRequest()->getParams());
        $sampleId = $this->getRequest()->getParam('sampleId');
        $sample = $this->_objectManager
            ->create("\Magento\Downloadable\Model\Sample")
            ->load($sampleId);

        $sampleFilePath = $this->_objectManager
            ->create("\Magento\Downloadable\Helper\File")
            ->getFilePath(
                $this->_objectManager
                    ->create("\Magento\Downloadable\Model\Link")
                    ->getBasePath(),
                $sample->getSampleFile()
            );
        $sampleFilePath = $this->_helperCatalog->getBasePath().'/'.$sampleFilePath;
        $fileArray = explode(DS, $sampleFilePath);
        $fileName = end($fileArray);

        $this->createLog("Chharo download sample file path : ", (array)[$sampleFilePath]);
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.android.package-archive');
        header('Content-Disposition: attachment; filename='.$fileName);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: '.filesize($sampleFilePath));
        ob_clean();
        flush();
        readfile($sampleFilePath);
    }
}
