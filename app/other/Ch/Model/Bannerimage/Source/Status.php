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

namespace Custom\Chharo\Model\Bannerimage\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status.
 */
class Status implements OptionSourceInterface
{
    /**
     * @var \Custom\Chharo\Model\Bannerimage
     */
    protected $_chharoBannerimage;

    /**
     * Constructor.
     *
     * @param \Custom\Chharo\Model\Bannerimage $chharoBannerimage
     */
    public function __construct(
        \Custom\Chharo\Model\Bannerimage $chharoBannerimage
    ) {
        $this->_chharoBannerimage = $chharoBannerimage;
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->_chharoBannerimage->getAvailableStatuses();
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
