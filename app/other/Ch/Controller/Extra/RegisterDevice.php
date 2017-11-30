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

namespace Custom\Chharo\Controller\Extra;

/**
 * Chharo API Extra controller.
 */
class RegisterDevice extends AbstractChharo
{


    /**
     * execute
     *
     * @return JSON
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $token = $this->getRequest()->getPost("token");
            $returnArray = [];
            try {
                if ($token != "") {
                    $deviceTokenModel =$this->_objectManager
                        ->create("\Custom\Chharo\Model\DeviceToken");
                    $deviceCollection = $deviceTokenModel->getCollection()->addFieldToFilter("token", $token);
                    if ($deviceCollection->getSize() == 0) {
                        $deviceTokenModel->setToken($token)->save();
                        $returnArray["message"] = __("Device Registered Succesfully");
                        $returnArray["error"] = 0;
                        $returnArray["isToken"] = 1;
                        return $this->getJsonResponse($returnArray);
                    } else {
                        $returnArray["message"] = __("Device Already Registered");
                        $returnArray["error"] = 1;
                        $returnArray["isToken"] = 1;
                        return $this->getJsonResponse($returnArray);
                    }
                } else {
                    $returnArray["message"] = __("Please Provide Token");
                    $returnArray["error"] = 1;
                    $returnArray["isToken"] = 1;
                    return $this->getJsonResponse($returnArray);
                }
            } catch (\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray["success"] = 0;
                $returnArray["message"] = __("Invalid Request.");
                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray["success"] = 0;
            $returnArray["message"] = __("Invalid Request.");
            return $this->getJsonResponse($returnArray);
        }
    }
}
