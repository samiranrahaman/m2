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

namespace Custom\Chharo\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Custom\Chharo\Helper\Data as HelperData;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\App\Emulation;

define('DS', '/');

/**
 * Chharo API Catalog controller.
 */
abstract class ApiController extends Action
{
    const VERSION_CODE = 209;

    protected $_errorResponse = null;

    /**
     * $_emulate.
     *
     * @var Magento\Store\Model\App\Emulate
     */
    protected $_emulate;

    /**
     * $_helperCatalog.
     *
     * @var Magento\Catalog\Helper\Catalog
     */
    protected $_helperCatalog;

    /**
     * __construct.
     *
     * @param Context    $context
     * @param HelperData $helper
     */
    public function __construct(
        Context $context,
        HelperData $helper,
        \Custom\Chharo\Helper\Catalog $helperCatalog,
        Emulation $emulate
    ) {
        $this->_helper = $helper;
        $this->_helperCatalog = $helperCatalog;
        $this->_emulate = $emulate;
        parent::__construct($context);
    }

    /**
     * checkRequestAutnticity check request authenticity.
     *
     * @return voiid|json string
     */
    protected function checkRequestAutnticity()
    {
        $actionName = strtolower($this->getRequest()->getActionName());
        
        $this->createLog('Chharo request Data for : '.$this->getRequest()->getActionName(), (array) $this->getRequest()->getPost());
        
        if ($actionName == 'uploadprofilepic') {
            return true;
        }

        if ($actionName == 'downloadsample' || $actionName == 'downloadlinksample') {
            return true;
        }
        
        $apiKey = $this->getRequest()->getPost('sessionId');
        $status = $this->_helper->authenticateApiCall($apiKey);
        $responseContent = [];
        if (!$status) {
            $this->createLog('Chharo request Data : ', ['authentication failed']);
            $responseContent['error'] = 5;
            $responseContent['message'] = __('Session id expired !');
            $this->createLog('Chharo request authenticity failed : ', $responseContent);
            $resultJson = $this->getJsonResponse($responseContent);

            $this->_errorResponse = $resultJson;

            return false;
        }

        return true;
    }

    /**
     * Check customer authentication.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        /*
         * check if authorization failed
         */
        if (!$this->checkRequestAutnticity()) {
            return $this->_errorResponse;
        }

        return parent::dispatch($request);
    }

    /**
     * getJsonResponse returns json response.
     *
     * @param array $responseContent
     *
     * @return JSON
     */
    protected function getJsonResponse($responseContent = [])
    {
        $this->createLog('Chharo Response Data : ', (array) $responseContent);
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseContent);

        return $resultJson;
    }

    /**
     * funtion for can reorder.
     *
     * @param array $order
     *
     * @return bool
     */
    public function canReorder(\Magento\Sales\Model\Order $order)
    {
        if (!$this->_helper->getConfigData(
            'sales/reorder/allow'
        )
        ) {
            return 0;
        }

        if (1) {   //customer always login
            return $order->canReorder();
        } else {
            return 1;
        }
    }

    public function createLog($msg, $object)
    {
        $this->_helper->debugChharo($msg, $object);
    }
}
