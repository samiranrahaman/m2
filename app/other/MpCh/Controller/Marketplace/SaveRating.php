<?php
/**
 * Custom Software.
 *
 * @category  Custom
 *
 * @author    Custom
 * @copyright Copyright (c) 2010-2017 Custom Software Private Limited (https://Custom.com)
 * @license   https://store.Custom.com/license.html
 */
namespace Custom\MpChharo\Controller\Marketplace;

/**
 * MpChharo API .
 */
class SaveRating extends AbstractMarketplace
{
    /**
     * execute.
     *
     * @return string JSON
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $returnArray = [];
                $sessionId = $this->getRequest()->getPost('sessionId');
                $feedprice = $this->getRequest()->getPost('feedprice');
                $feedvalue = $this->getRequest()->getPost('feedvalue');
                $feedquality = $this->getRequest()->getPost('feedquality');
                $proownerid = $this->getRequest()->getPost('proownerid');
                $profileurl = $this->getRequest()->getPost('profileurl');
                $feednickname = $this->getRequest()->getPost('feednickname');
                $feedsummary = $this->getRequest()->getPost('feedsummary');
                $feedreview = $this->getRequest()->getPost('feedreview');
                $userid = $this->getRequest()->getPost('userid');
                $useremail = $this->getRequest()->getPost('useremail');
                if (!isset($userid)) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Invalid customer')
                    );
                }
                /*
                 * $initialEnvironmentInfo store emulation start
                 */

                $data = [];
                $data['created_at'] = $this->_objectManager->create("Magento\Framework\Stdlib\DateTime\DateTime")->gmtDate();
                $data['feed_price'] = $feedprice;
                $data['feed_value'] = $feedvalue;
                $data['feed_quality'] = $feedquality;
                $data['seller_id'] = $proownerid;
                $data['shop_url'] = $profileurl;
                $data['feed_nickname'] = $feednickname;
                $data['feed_summary'] = $feedsummary;
                $data['feed_review'] = $feedreview;
                $data['seller_id'] = $userid;
                $data['buyer_email'] = $useremail;
                $customerId = $userid;
                $sellerId = $proownerid;
                $feedbackcount = 0;
                $collectionfeed = $this->_objectManager->create("\Custom\Marketplace\Model\FeedbackcountFactory")
                ->create()
                ->getCollection()
                ->addFieldToFilter('buyer_id', $customerId)
                ->addFieldToFilter('seller_id', $sellerId);
                foreach ($collectionfeed as $value) {
                    $feedcountid = $value->getEntityId();
                    $ordercount = $value->getOrderCount();
                    $feedbackcount = $value->getFeedbackCount();
                    $value->setFeedbackCount($feedbackcount + 1);
                    $value->save();
                }
                $this->_objectManager->create("\Custom\Marketplace\Model\FeedbackFactory")->create()->setData($data)->save();

                $returnArray['message'] = __('Your review successfully saved');
                $returnArray['success'] = 1;

                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $returnArray['success'] = 0;
                $returnArray['message'] = __($e->getMessage());

                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray['success'] = 0;
            $returnArray['message'] = __('Invalid Request.');

            return $this->getJsonResponse($returnArray);
        }
    }
}
