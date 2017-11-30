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

namespace Custom\Chharo\Model\Notification\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type.
 */
class Type implements OptionSourceInterface
{
    /**
     * @var \Custom\Chharo\Model\Notification
     */
    protected $_chharoNotification;

    /**
     * Constructor.
     *
     * @param \Custom\Chharo\Model\Notification $chharoNotification
     */
    public function __construct(
        \Custom\Chharo\Model\Notification $chharoNotification
    ) {
        $this->_chharoNotification = $chharoNotification;
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->_chharoNotification->getAvailableTypes();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }

        return $options;
    }
}
