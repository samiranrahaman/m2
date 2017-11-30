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

namespace Custom\Chharo\Controller\Adminhtml\Categoryimages;

use Magento\Framework\Controller\ResultFactory;
use Custom\Chharo\Controller\RegistryConstants;
use Custom\Chharo\Api\Data\CategoryimagesInterface;

class Delete extends \Custom\Chharo\Controller\Adminhtml\Categoryimages
{
    /**
     * Delete category images action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $formKeyIsValid = $this->_formKeyValidator->validate($this->getRequest());
        $isPost = $this->getRequest()->isPost();
        if (!$formKeyIsValid || !$isPost) {
            $this->messageManager->addError(__('Category image record could not be deleted.'));
            return $resultRedirect->setPath('chharo/categoryimages/index');
        }

        $categoryimagesId = $this->initCurrentCategoryimages();
        if (!empty($categoryimagesId)) {
            try {
                $this->_categoryimagesRepository->deleteById($categoryimagesId);
                $this->messageManager->addSuccess(__('Category image record has been deleted.'));
            } catch (\Exception $exception) {
                $this->messageManager->addError($exception->getMessage());
            }
        }

        /**
 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect 
*/
        return $resultRedirect->setPath('chharo/categoryimages/index');
    }

    /**
     * Categoryimages initialization
     *
     * @return string categoryimages id
     */
    protected function initCurrentCategoryimages()
    {
        $categoryimagesId = (int)$this->getRequest()->getParam('id');

        if ($categoryimagesId) {
            $this->_coreRegistry->register(
                RegistryConstants::CURRENT_CATEGORYIMAGES_ID,
                $categoryimagesId
            );
        }

        return $categoryimagesId;
    }
}
