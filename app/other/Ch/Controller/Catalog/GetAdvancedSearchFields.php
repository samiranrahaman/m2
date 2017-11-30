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

namespace Custom\Chharo\Controller\Catalog;

use Magento\Framework\App\Action\Context;
use Custom\Chharo\Helper\Data as HelperData;
use Custom\Chharo\Helper\Catalog as HelperCatalog;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\App\Emulation;

/**
 * Chharo API Catalog controller.
 */
class GetAdvancedSearchFields extends \Custom\Chharo\Controller\ApiController
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
     * execute category list
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $returnArray = [];
            $storeId = $this->getRequest()->getPost("storeId");
            if ($storeId == "") {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                
                /**
                 * $initialEnvironmentInfo store emulation start
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                
                $attributes = $this->_objectManager
                    ->create("\Magento\CatalogSearch\Model\Advanced")
                    ->getAttributes();

                foreach ($attributes as $_attribute) {
                    $each = [];
                    $_code = $_attribute->getAttributeCode();
                    $label = $_attribute->getStoreLabel();
                    $each["label"] = $label;
                    $each["inputType"] = $this->_helperCatalog->getAttributeInputType($_attribute);
                    $each["attributeCode"] = $_code;

                    $each["maxQueryLength"] = $this->_helperCatalog
                        ->getMaxQueryLength();
                    $each["title"] = $this->_helperCatalog->stripTags($label);
                    $each["options"] = $_attribute->getSource()->getAllOptions(true);
                    $returnArray[] = $each;
                }
               
                /**
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                
                return $this->getJsonResponse($returnArray);
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

    private function getCategoryList()
    {
        return [];
    }
}
