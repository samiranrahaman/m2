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
class GetSearchTerms extends AbstractChharo
{


    /**
     * execute
     *
     * @return JSON
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $storeId = $this->getRequest()->getPost("storeId");
            $returnArray = [];
            if ($storeId == "") {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                /**
                 * $initialEnvironmentInfo store emulation start
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $termBlock = $this->_objectManager
                    ->create("\Magento\Search\Model\ResourceModel\Query\Collection")
                    ->addFieldToFilter(
                        "store_id",
                        [
                        [
                            "finset" => [$storeId]
                        ]
                        ]
                    )->setPopularQueryFilter($storeId);

                $maxPopularity = $termBlock->getFirstitem()->getPopularity();
                $minPopularity = $termBlock->getFirstitem()->getPopularity();
                $range = $maxPopularity - $minPopularity;
                $range = $range == 0 ? 1 : $range;

                if (sizeof($termBlock) > 0) {
                    foreach ($termBlock as $_term) {
                        $eachTerm = [];
                        $eachTerm["ratio"] =
                        ((($_term->getPopularity() - $minPopularity) / $range));
                        if ($eachTerm["ratio"] < 0) {
                            $eachTerm["ratio"] = 0;
                        } else {
                            $eachTerm["ratio"] *= 70 ;
                            $eachTerm["ratio"] += 75 ;
                        }
                        $eachTerm["term"] = $this->_helperCatalog
                            ->stripTags($_term->getQueryText());
                        $returnArray[] = $eachTerm;
                    }
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
}
