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

namespace Custom\Chharo\Controller\Index;

use Magento\Framework\App\Action\Context;
use Custom\Chharo\Helper\Data as HelperData;
use Custom\Chharo\Helper\Catalog as HelperCatalog;
use Magento\Store\Model\App\Emulation;

/**
 * Chharo API Upload profile pic controller.
 */
class UploadProfilePic extends \Custom\Chharo\Controller\ApiController
{
    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        HelperData $helper,
        HelperCatalog $helperCatalog,
        Emulation $emulate
    ) {
        parent::__construct($context, $helper, $helperCatalog, $emulate);
    }

    /**
     * execute upload profile picture.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $filesArr = (array) $this->getRequest()->getFiles();
        $this->createLog("Chharo upload request: ".get_class($this)." : ", (array)$filesArr);
        if (isset($filesArr)) {
            try {
                $data = $this->getRequest()->getParams();
                $customerId = $this->getRequest()->getParam('customerId');
                $this->_helperCatalog->uploadPicture($filesArr, $customerId, time().$customerId.'-profile', 'profile');
                if (isset($data['width'])) {
                    $this->_helperCatalog->resizeAndCache($data['customerId'], $data['width']);
                }
            } catch (\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ", (array)$e->getTrace());
                $returnArray['success'] = 0;
                $returnArray['message'] = __('Invalid request');

                return $this->getJsonResponse($returnArray);
            }
        }
    }
}
