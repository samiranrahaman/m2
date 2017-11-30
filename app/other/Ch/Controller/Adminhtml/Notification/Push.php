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

namespace Custom\Chharo\Controller\Adminhtml\Notification;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Custom\Chharo\Model\ResourceModel\Notification\CollectionFactory;
use Custom\Chharo\Api\NotificationRepositoryInterface;

/**
 * Class push.
 */
class Push extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var NotificationRepositoryInterface
     */
    protected $_notificationRepository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @param Context                        $context
     * @param Filter                         $filter
     * @param CollectionFactory              $collectionFactory
     * @param NotificationRepositoryInterface $notificationRepository
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        NotificationRepositoryInterface $notificationRepository,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_notificationRepository = $notificationRepository;
        $this->_date = $date;
        parent::__construct($context);
    }

    /**
     * Execute action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     *
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $notificationsUpdated = 0;
        $coditionArr = [];
        foreach ($collection->getAllIds() as $key => $notificationId) {
            $model = $this->_notificationRepository->getById($notificationId);
            
            /**
             * push notifiction for IOS 
             */
            $baseTmpPath = 'chharo/notification/';
            $storeManager = $this->_objectManager->create('\Magento\Store\Model\StoreManagerInterface');
            $target = $storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ).$baseTmpPath;
            if ($model->getFilename() != "") {
                $bannerUrl = $target.$model->getFilename();
            } else {
                $bannerUrl = "";
            }
            $message = [
                "title"             => $model->getTitle(),
                "message"           => $model->getContent(),
                "id"                => $model->getId(),
                "notificationType"  => $model->getType(),
                "banner_url"        => $bannerUrl,
                "store_id"          => $model->getStoreId()
            ];

            $iosMsg = [
                "title"=> $model->getTitle(),
                "body"=> $model->getContent()
            ];

            if ($model->getType() == 'category' && $model->getProCatId() != "") {
                //for category
                $message["categoryName"] = $this->_objectManager->get('Magento\Catalog\Model\ResourceModel\Category')->getAttributeRawValue($model->getProCatId(), "name", 1);

                $message["categoryId"] = $model->getProCatId();

            } elseif ($model->getType() == 'product' && $model->getProCatId() != "") {
                //for product
                $message["productName"] = $this->_objectManager->get('Magento\Catalog\Model\ResourceModel\Product')->getAttributeRawValue($model->getProCatId(), "name", 1);
                $message["productId"] = $model->getProCatId();
            }

            $url = "https://fcm.googleapis.com/fcm/send";
            $authKey = $this->_objectManager->get("Custom\Chharo\Helper\Data")->getConfigData("chharo/notification/apikey");
            $headers = [
                "Authorization: key=".$authKey,
                "Content-Type: application/json",
            ];

            $androidTokenCollection = $this->_objectManager->create("\Custom\Chharo\Model\AndroidTokenFactory")->create()
            ->getCollection();

            $error = 0;
            $errorMsg = [];
            if ($androidTokenCollection->getSize() > 0) {
                foreach ($androidTokenCollection as $eachToken) {
                    $fields = [
                        "to"                => $eachToken->getToken(),
                        "data"              => $message,
                        'notification'      => $iosMsg,
                        "time_to_live"      => 30,
                        "delay_while_idle"  => true
                    ];
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                    $result = curl_exec($ch);
                    curl_close($ch);
                    if ($result === false) {
                        $error = 1;
                        $errorMsg[] = curl_error($ch);
                    } else {
                        $result = json_decode($result, true);
                    }

                }
                if ($error == 1)
                    $this->messageManager->addError("Android Related Error : ".implode(" | ", $errorMsg));
                else
                    $this->messageManager->addSuccess(__("Android push notifications sent"));
            } else {
                $this->messageManager->addError(__("no android devide tokens found"));
            }
            


            /**
             * push notifiction for IOS 
             */

            // $streamContext = stream_context_create();
            // $APNCertificateFileName = $this->_objectManager->get("Custom\Chharo\Helper\Data")
            // ->getConfigData("chharo/iosnotification/certificateName");

            // stream_context_set_option($streamContext, "ssl", "local_cert", $storeManager->getStore()->getBaseUrl().'/'.$APNCertificateFileName);
            // stream_context_set_option($streamContext, "ssl", "passphrase", "");
            // if ($model->getType() == 'category' && $model->getProCatId() != "") {
            //     //for category
            //     $payload = '{
            //         "aps" :{
            //             "content-available" : 1,
            //             "alert" : "'.$model->getTitle().'",
            //             "badge" : 1, 
            //             "sound" : "default",
            //             "categoryName" : "'.$this->_objectManager->get('Magento\Catalog\Model\ResourceModel\Category')->getAttributeRawValue($model->getProCatId(), "name", 1).'",
            //             "categoryId" : "'.$model->getProCatId().'",
            //             "message" : "'.$model->getContent().'",
            //             "notificationId" : "'.$model->getId().'",
            //             "notificationType" : "'.$model->getType().'"
            //         }
            //     }';
            // } elseif ($model->getType() == 'product' && $model->getProCatId() != "") {
            //     //for product
            //     $payload = '{
            //         "aps" :{
            //             "content-available" : 1,
            //             "alert" : "'.$model->getTitle().'",
            //             "badge" : 1, 
            //             "sound" : "default",
            //             "productName" : "'.$this->_objectManager->get('Magento\Catalog\Model\ResourceModel\Product')->getAttributeRawValue($model->getProCatId(), "name", 1).'",
            //             "productId" : "'.$model->getProCatId().'",
            //             "message" : "'.$model->getContent().'",
            //             "notificationId" : "'.$model->getId().'",
            //             "notificationType" : "'.$model->getType().'"
            //          }
            //     }';
            // } elseif ($model->getType() == 'collection') {
            //     //for other
            //     $payload = '{
            //         "aps" :{
            //             "content-available" : 1,
            //             "alert" : "'.$model->getTitle().'",
            //             "badge" : 1, 
            //             "sound" : "default",
            //             "message" : "'.$model->getContent().'",
            //             "notificationId" : "'.$model->getId().'",
            //             "notificationType" : "'.$model->getType().'"
            //          }
            //     }';
            // } elseif ($model->getType() == 'others') {
            //     //for custom collection
            //     $payload = '{
            //         "aps" :{
            //             "content-available" : 1,
            //             "alert" : "'.$model->getTitle().'",
            //             "badge" : 1, 
            //             "sound" : "default",
            //             "message" : "'.$model->getContent().'",
            //             "notificationId" : "'.$model->getId().'",
            //             "notificationType" : "'.$model->getType().'"
            //          }
            //     }';
            // }
            // $deviceTokenCollection = $this->_objectManager->create("\Custom\Chharo\Model\DeviceTokenFactory")->getCollection();
            // // foreach($deviceTokenCollection as $dvd) {
            // //     $dvd->delete();
            // // }die;
            // foreach ($deviceTokenCollection as $eachRow) {
            //     if ($this->_objectManager->get("Custom\Chharo\Helper\Data")
            // ->getConfigData("chharo/iosnotification/isSandbox") != 0) {
            //         $client = stream_socket_client("ssl://gateway.sandbox.push.apple.com:2195", $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $streamContext);
            //     } else {
            //         $client = stream_socket_client("ssl://gateway.push.apple.com:2195", $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $streamContext);
            //     }
            //     if (!$client) {
            //         $result = false;
            //         $this->messageManager->addError("Failed to connect:".$err.$errstr);
            //     }
            //     $message = chr(0).pack("n", 32).pack("H*", $eachRow->getToken()).pack("n", strlen($payload)).$payload;
            //     $result = fwrite($client, $message, strlen($message));
            //     fclose($client);
            // }
            
        }



        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        return $resultRedirect->setPath('chharo/notification/index');
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Custom_Chharo::notification');
    }

    
}
