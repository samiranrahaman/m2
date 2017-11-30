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

namespace Custom\Chharo\Controller\Extra;

/**
 * Chharo API Extra controller.
 */
class RefreshAndroidToken extends AbstractChharo
{
    /**
     * execute.
     *
     * @return JSON
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $wholeData = $this->getRequest()->getPost();
            $returnArray = array();
            $returnArray['success'] = 0;
            $returnArray['message'] = '';
            try {
                $customerId = isset($wholeData['customerId']) ? $wholeData['customerId'] : '';
                $token = isset($wholeData['token']) ? $wholeData['token'] : '';
                $id = $this->_objectManager->get("\Custom\Chharo\Helper\Token")->setAndroidToken($customerId, $token);
                $returnArray['success'] = 1;
                $returnArray['message'] = '';

                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray['success'] = 0;
                $returnArray['message'] = __("invalid request");

                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray['success'] = 0;
            $returnArray['message'] = __('Invalid Request.');

            return $this->getJsonResponse($returnArray);
        }
    }
}
