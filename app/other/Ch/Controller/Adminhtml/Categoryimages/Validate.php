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

class Validate extends \Custom\Chharo\Controller\Adminhtml\Categoryimages
{
    /**
     * Customer validation
     *
     * @param  \Magento\Framework\DataObject $response
     * @return CategoryimagesInterface|null
     */
    protected function _validateCategoryimages($response)
    {
        $categoryimages = null;
        $errors = [];

        try {
            /**
 * @var CategoryimagesInterface $categoryimages 
*/
            $categoryimages = $this->categoryimagesDataFactory->create();

            $data = $this->getRequest()->getParams();
            $dataResult = $data['chharo_categoryimages'];
            $errors = [];
            if (!isset($dataResult['icon'][0]['name'])) {
                $errors[] =  __('Please upload category icon image.');
            }
            if (!isset($dataResult['banner'][0]['name'])) {
                $errors[] =  __('Please upload category banner image.');
            }
            if (isset($dataResult['category_id'])) {
                if ($dataResult['category_id']) {
                    try {
                        $this->categoryRepositoryInterface->get($dataResult['category_id']);
                    } catch (\Exception $exception) {
                        $errors[] =  __('Requested category doesn\'t exist');
                    }
                }
            } else {
                $errors[] =  __('Category id should be set.');
            }
        } catch (\Magento\Framework\Validator\Exception $exception) {
            $exceptionMsg = $exception->getMessages(
                \Magento\Framework\Message\MessageInterface::TYPE_ERROR
            );
            /**
             * @var $error Error
             */
            foreach ($exceptionMsg as $error) {
                $errors[] = $error->getText();
            }
        }

        if ($errors) {
            $messages = $response->hasMessages() ? $response->getMessages() : [];
            foreach ($errors as $error) {
                $messages[] = $error;
            }
            $response->setMessages($messages);
            $response->setError(1);
        }

        return $categoryimages;
    }

    /**
     * AJAX customer validation action
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $response->setError(0);

        $categoryimages = $this->_validateCategoryimages($response);

        $resultJson = $this->resultJsonFactory->create();
        if ($response->getError()) {
            $response->setError(true);
            $response->setMessages($response->getMessages());
        }

        $resultJson->setData($response);
        return $resultJson;
    }
}
