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

namespace Custom\Chharo\Controller\Wallet;

use Magento\Framework\App\Action\Context;
use Custom\Chharo\Helper\Data as HelperData;
use Custom\Chharo\Helper\Catalog as HelperCatalog;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\App\Emulation;
use Custom\Walletsystem\Helper\Data as WalletHelper;
use Custom\Walletsystem\Model\WalletTransferData;
use Custom\Walletsystem\Model\WalletUpdateData;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use \Magento\Framework\Unserialize\Unserialize;

/**
 * Chharo API - accept payments
 */
class TransferMoney extends \Custom\Chharo\Controller\ApiController
{
	protected $_customerFactory;
	
	/**
     * @var Custom\Walletsystem\Helper\Data
     */
    protected $_walletHelper;
    /**
     * @var Custom\Walletsystem\Model\WalletTransferData
     */
    protected $_waletTransfer;
    /**
     * @var Custom\Walletsystem\Model\WalletUpdateData
     */
    protected $_walletUpdate;
    /**
     * @var Encryptor
     */
    private $encryptor;
    /**
     * @var Unserialize
     */
    protected $unserialize;
	
    public function __construct(
        Context $context,
        HelperData $helper,
        HelperCatalog $helperCatalog,
        Emulation $emulate,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		WalletHelper $walletHelper,
		\Custom\Walletsystem\Model\WalletTransferData $walletSession,
        Encryptor $encryptor,
        \Custom\Walletsystem\Model\WalletUpdateData $walletUpdate,
        Unserialize $unserialize
    ) {
		$this->_customerFactory = $customerFactory;
		$this->_walletHelper = $walletHelper;
		$this->_waletTransfer = $walletSession;
        $this->encryptor = $encryptor;
        $this->_walletUpdate = $walletUpdate;
        $this->_unserialize = $unserialize;
        parent::__construct($context, $helper, $helperCatalog, $emulate);
    }

    /**
     * execute category list.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
            try {
				
                $params['sender_id'] = $this->getRequest()->getPost('senderId');
				$params['reciever_id'] = $this->getRequest()->getPost('receiverId');
				//$amount = $this->getRequest()->getPost('amount');
                $params['amount'] = (float)$this->getRequest()->getPost('amount');
				
				$walletHelper = $this->_walletHelper;
			    
				$params['curr_code'] = $walletHelper->getCurrentCurrencyCode();
                $fromCurrency = $walletHelper->getCurrentCurrencyCode();
                $toCurrency = $walletHelper->getBaseCurrencyCode();
                $amount = $params['amount'];
                $transferAmount = $walletHelper->getwkconvertCurrency($fromCurrency, $toCurrency, $amount);
                $totalAmount = $walletHelper->getWalletTotalAmount($params['sender_id']);
                if ($transferAmount <= $totalAmount) {
                    $params['base_amount'] = $transferAmount;
                    $params['curr_amount'] = $params['amount'];
                    $this->SendAmountToWallet($params);
                    $this->DeductAmountFromWallet($params);
                }else{
					
                $returnArray["success"] = 0;
				$returnArray["message"] = "Insufficient Funds. Add Money to your wallet account to proceed. ";
				
                return $this->getJsonResponse($returnArray);
					
				}
             
				
                $returnArray["success"] = 1;
                return $this->getJsonResponse($returnArray);
				
            } catch (\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray['success'] = 0;
                $returnArray['message'] = __('Invalid request');

                return $this->getJsonResponse($returnArray);
            }
        }
		
		
		public function SendAmountToWallet($params)
		{
			$customerModel = $this->_walletHelper->getCustomerByCustomerId($params['sender_id']);
			$senderName = $customerModel->getFirstname()." ".$customerModel->getLastname();
			//if ($params['walletnote']=='') {
				//$params['walletnote'] = __("Transfer by ");
			//}
			$transferAmountData = [
				'customerid' => $params['reciever_id'],
				'walletamount' => $params['base_amount'],
				'walletactiontype' => 'credit',
				'curr_code' => $params['curr_code'],
				'curr_amount' => $params['curr_amount'],
				'walletnote' => "",
				'sender_id' => $params['sender_id'],
				'sender_type' => 4
			];
			$msg = __(
				'%1 amount %2ed by %3.  He added a note for the transaction: %4',
				$this->_walletHelper->getformattedPrice($transferAmountData['walletamount']),
				$transferAmountData['walletactiontype'],
				$senderName,
				""
			);
			$adminMsg = __(
				"'s account is updated with the %1 amount %2ed by %3. He added a note for the transaction: %4",
				$this->_walletHelper->getformattedPrice($transferAmountData['walletamount']),
				$transferAmountData['walletactiontype'],
				$senderName,
				""
			); 
			$this->_walletUpdate->creditAmount($params['reciever_id'], $transferAmountData, $msg, $adminMsg);
		}
		// deduct amount from sender's wallet
		public function DeductAmountFromWallet($params)
		{
			$customerModel = $this->_walletHelper->getCustomerByCustomerId($params['reciever_id']);
			$recieverName = $customerModel->getFirstname()." ".$customerModel->getLastname();
			
			$transferAmountData = [
				'customerid' => $params['reciever_id'],
				'walletamount' => $params['base_amount'],
				'walletactiontype' => 'debit',
				'curr_code' => $params['curr_code'],
				'curr_amount' => $params['curr_amount'],
				'walletnote' => __(""),
				'sender_id' => $params['sender_id'],
				'sender_type' => 4
			];
			$msg = __(
				'You have transfered %1 amount to %2. you added a note on the transaction: %3',
				$this->_walletHelper->getformattedPrice($transferAmountData['walletamount']),
				$recieverName,
				__("")
			);
			$adminMsg = __(
				"'s account is updated with the %1 amount transferd to %2. He added a note for the transaction: %3",
				$this->_walletHelper->getformattedPrice($transferAmountData['walletamount']),
				$recieverName,
				__("")
			);
			$this->_walletUpdate->debitAmount($params['sender_id'], $transferAmountData, $msg, $adminMsg);
    }
		
		
}