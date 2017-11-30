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

namespace Custom\Chharo\Block\Adminhtml\Edit\Notification;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Custom\Chharo\Block\Adminhtml\Edit\GenericButton;

/**
 * Class PushButton.
 */
class PushButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [
            'label' => __('Push Notification'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];

        return $data;
    }
}
