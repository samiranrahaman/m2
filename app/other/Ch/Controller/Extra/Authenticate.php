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

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Custom\Chharo\Helper\Data as HelperData;
use Custom\Chharo\Model\ChharoAuthFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Chharo API Authenticate controller.
 */
class Authenticate extends Action
{

     /**
      * @var HelperData
      */
    protected $_helper;

    /**
     * $_chharoAuthFactory
     *
     * @var Custom\Chharo\Model\ChharoAuthFactory;
     */
    protected $_chharoAuthFactory;

    /**
     * $_sessionManager
     *
     * @var \Custom\Chharo\Model\ApiSession
     */
    protected $_sessionManager;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     * @param HelperData  $helper
     */
    public function __construct(
        Context $context,
        HelperData $helper,
        \Custom\Chharo\Model\ApiSession $sessionManager,
        ChharoAuthFactory $chharoAuthFactory
    ) {
    
        $this->_helper = $helper;
        $this->_chharoAuthFactory = $chharoAuthFactory;
        $this->_sessionManager = $sessionManager;
        parent::__construct($context);
    }

    /**
     * Chharo API Authenticate Action.
     *
     * @return array
     */
    public function execute()
    {
        $returnArray = [];
        //custom log code
        $this->_helper->debugChharo("Chharo authentication request", (array)$this->getRequest()->getPost());
					//code by shiva
			$link = fopen("d:\atest.txt","a+");
			fwrite($link, $this->getRequest()->getPost("username")."hi");
			fclose($link);
			//*********

        if ($this->getRequest()->getPost()) {
            $userName = $this->getRequest()->getPost("username");
			
            $password = $this->getRequest()->getPost("password");
            /**
             * $isAutheticated status of authorization
             *
             * @var boolean true|false
             */
            $isAutheticated = $this->checkCredentials($userName, $password);
            if ($isAutheticated) {
                /**
                 * $sessionId generate api key
                 *
                 * @var string
                 */
                $sessionId = $this->_sessionManager->generateSessionId();
                $status = $this->setApiKey($sessionId);
                if ($status) {
                    $responseContent["error"] = 0;
                    $responseContent["sessionId"] = $sessionId;
                } else {
                    $responseContent["error"] = 1;
                    $responseContent["message"] = __("There is some error in processing your request");
                }
            } else {
                $responseContent["error"] = 1;
                $responseContent["message"] = __("Invalid username or password.");
            }
        } else {
            $responseContent["error"] = 1;
            $responseContent["message"] = __("Invalid Request.");
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseContent);
        return $resultJson;
    }

    /**
     * setApiKey set api key
     *
     * @param string $apiKey unique api key
     *
     * @return boolean true|false
     */
    protected function setApiKey($apiKey)
    {
        try {
            $this->_sessionManager->setApiId($apiKey);
            return true;
        } catch (\Exception $e) {
            $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
            return false;
        }
    }

    /**
     * Check if post credentials are correct.
     *
     * @return boolean true|false
     */
    public function checkCredentials($userName, $password)
    {
        $configUsername = $this->_helper->getUsername();
        $configPassword = $this->_helper->getPassword();
        if ($configUsername == $userName && $configPassword == $password) {
            return true;
        }
        return false;
    }

    /**
     * generate a token.
     *
     * @deprecated method no more used 
     *
     * @return string
     */
    private function generateApiKey()
    {
        return md5(uniqid(rand(), true));
    }
}
