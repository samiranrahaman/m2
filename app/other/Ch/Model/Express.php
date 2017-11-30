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
namespace Custom\Chharo\Model;

/**
 * Chharo Paypal model
 */
class Express extends \Magento\Paypal\Model\Express
{
    /**
     * Authorize payment
     *
     * @param \Magento\Framework\DataObject|\Magento\Payment\Model\InfoInterface|Payment $payment
     * @param float $amount
     * @return $this
     */
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $om->get('Magento\Framework\App\RequestInterface');
        if ($request->getPost("sessionId")) {
            return true;
        }
        parent::authorize($payment, $amount);
    }
}
