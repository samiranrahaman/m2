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

namespace Custom\Chharo\Controller\Adminhtml\Bannerimage;

use Magento\Framework\Controller\ResultFactory;
use Custom\Chharo\Controller\RegistryConstants;
use Custom\Chharo\Api\Data\BannerimageInterface;

class Delete extends \Custom\Chharo\Controller\Adminhtml\Bannerimage
{
    /**
     * Delete banner action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $formKeyIsValid = $this->_formKeyValidator->validate($this->getRequest());
        $isPost = $this->getRequest()->isPost();
        if (!$formKeyIsValid || !$isPost) {
            $this->messageManager->addError(__('Banner could not be deleted.'));
            return $resultRedirect->setPath('chharo/bannerimage/index');
        }

        $bannerimageId = $this->initCurrentBanner();
        if (!empty($bannerimageId)) {
            try {
                $this->_bannerimageRepository->deleteById($bannerimageId);
                $this->messageManager->addSuccess(__('Banner has been deleted.'));
            } catch (\Exception $exception) {
                $this->messageManager->addError($exception->getMessage());
            }
        }

        /**
 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect 
*/
        return $resultRedirect->setPath('chharo/bannerimage/index');
    }

    /**
     * Banner initialization
     *
     * @return string banner id
     */
    protected function initCurrentBanner()
    {
        $bannerimageId = (int)$this->getRequest()->getParam('id');

        if ($bannerimageId) {
            $this->_coreRegistry->register(RegistryConstants::CURRENT_BANNER_ID, $bannerimageId);
        }

        return $bannerimageId;
    }
}
