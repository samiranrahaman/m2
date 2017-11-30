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

use Custom\Chharo\Controller\RegistryConstants;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Custom\Chharo\Controller\Adminhtml\Featuredcategories
{
    /**
     * Save featuredcategories action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $returnToEdit = false;
        $originalRequestData = $this->getRequest()->getPostValue();
        $featuredcategoriesId = isset($originalRequestData['chharo_featuredcategories']['id'])
            ? $originalRequestData['chharo_featuredcategories']['id']
            : null;
        if ($originalRequestData) {
            try {
                $featuredcategoriesData = $originalRequestData['chharo_featuredcategories'];
                $featuredcategoriesData['filename'] = $this->getFeaturedcategoriesImageName(
                    $featuredcategoriesData
                );
                $featuredcategoriesData['store_id'] = $this->getFeaturedcategoriesStoreId(
                    $featuredcategoriesData
                );
                $request = $this->getRequest();
                $isExistingFeaturedcategories = (bool) $featuredcategoriesId;
                $featuredcategories = $this->featuredcategoriesDataFactory->create();
                if ($isExistingFeaturedcategories) {
                    $currentFeaturedcategories = $this->_featuredcategoriesRepository->getById(
                        $featuredcategoriesId
                    );
                    $featuredcategoriesData['id'] = $featuredcategoriesId;
                }
                $featuredcategoriesData['updated_at'] = $this->_date->gmtDate();
                if (!$isExistingFeaturedcategories) {
                    $featuredcategoriesData['created_at'] = $this->_date->gmtDate();
                }
                $featuredcategories->setData($featuredcategoriesData);

                // Save featuredcategories
                if ($isExistingFeaturedcategories) {
                    $this->_featuredcategoriesRepository->save($featuredcategories);
                } else {
                    $featuredcategories = $this->_featuredcategoriesRepository->save($featuredcategories);
                    $featuredcategoriesId = $featuredcategories->getId();
                }
                $this->_getSession()->unsFeaturedcategoriesFormData();
                // Done Saving featuredcategories, finish save action
                $this->_coreRegistry->register(
                    RegistryConstants::CURRENT_FEATUREDCATEGORIES_ID,
                    $featuredcategoriesId
                );
                $this->messageManager->addSuccess(__('You saved the featuredcategories.'));
                $returnToEdit = (bool) $this->getRequest()->getParam('back', false);
            } catch (\Magento\Framework\Validator\Exception $exception) {
                $messages = $exception->getMessages();
                if (empty($messages)) {
                    $messages = $exception->getMessage();
                }
                $this->_addSessionErrorMessages($messages);
                $this->_getSession()->setFeaturedcategoriesFormData($originalRequestData);
                $returnToEdit = true;
            } catch (\Exception $exception) {
                $this->messageManager->addException(
                    $exception,
                    __(
                        'Something went wrong while saving the featuredcategories. %1',
                        $exception->getMessage()
                    )
                );
                $this->_getSession()->setFeaturedcategoriesFormData($originalRequestData);
                $returnToEdit = true;
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($returnToEdit) {
            if ($featuredcategoriesId) {
                $resultRedirect->setPath(
                    'chharo/featuredcategories/edit',
                    ['id' => $featuredcategoriesId, '_current' => true]
                );
            } else {
                $resultRedirect->setPath(
                    'chharo/featuredcategories/new',
                    ['_current' => true]
                );
            }
        } else {
            $resultRedirect->setPath('chharo/featuredcategories/index');
        }

        return $resultRedirect;
    }

    private function getFeaturedcategoriesImageName($featuredcategoriesData)
    {
        if (isset($featuredcategoriesData['filename'][0]['name'])) {
            if (isset($featuredcategoriesData['filename'][0]['name'])) {
                return $featuredcategoriesData['filename'] = $featuredcategoriesData['filename'][0]['name'];
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Please upload featuredcategories image.')
                );
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Please upload featuredcategories image.')
            );
        }
    }

    private function getFeaturedcategoriesStoreId($featuredcategoriesData)
    {
        if (isset($featuredcategoriesData['store_id'])) {
            return $featuredcategoriesData['store_id'] = implode(',', $featuredcategoriesData['store_id']);
        } else {
            return $featuredcategoriesData['store_id'] = 0;
        }
    }
}
