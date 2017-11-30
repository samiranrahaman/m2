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

namespace Custom\Chharo\Controller\Adminhtml\Featuredcategories;

use Magento\Framework\Controller\ResultFactory;
use Custom\Chharo\Controller\RegistryConstants;
use Custom\Chharo\Api\Data\FeaturedcategoriesInterface;

class Delete extends \Custom\Chharo\Controller\Adminhtml\Featuredcategories
{
    /**
     * Delete featuredcategories action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $formKeyIsValid = $this->_formKeyValidator->validate($this->getRequest());
        $isPost = $this->getRequest()->isPost();
        if (!$formKeyIsValid || !$isPost) {
            $this->messageManager->addError(__('Featuredcategories could not be deleted.'));
            return $resultRedirect->setPath('chharo/featuredcategories/index');
        }

        $featuredcategoriesId = $this->initCurrentFeaturedcategories();
        if (!empty($featuredcategoriesId)) {
            try {
                $this->_featuredcategoriesRepository->deleteById($featuredcategoriesId);
                $this->messageManager->addSuccess(__('Featuredcategories has been deleted.'));
            } catch (\Exception $exception) {
                $this->messageManager->addError($exception->getMessage());
            }
        }

        /**
 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect 
*/
        return $resultRedirect->setPath('chharo/featuredcategories/index');
    }

    /**
     * Featuredcategories initialization
     *
     * @return string featuredcategories id
     */
    protected function initCurrentFeaturedcategories()
    {
        $featuredcategoriesId = (int)$this->getRequest()->getParam('id');

        if ($featuredcategoriesId) {
            $this->_coreRegistry->register(
                RegistryConstants::CURRENT_FEATUREDCATEGORIES_ID,
                $featuredcategoriesId
            );
        }

        return $featuredcategoriesId;
    }
}
