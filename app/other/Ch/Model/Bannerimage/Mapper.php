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

namespace Custom\Chharo\Model\Bannerimage;

use Custom\Chharo\Api\Data\BannerimageInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Convert\ConvertArray;

/**
 * Class Mapper converts Address Service Data Object to an array
 */
class Mapper
{
    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    private $extensibleDataObjectConverter;

    /**
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(ExtensibleDataObjectConverter $extensibleDataObjectConverter)
    {
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * Convert address data object to a flat array
     *
     * @param  BannerimageInterface $Bannerimage
     * @return array
     */
    public function toFlatArray(BannerimageInterface $bannerimage)
    {
        $flatArray = $this->extensibleDataObjectConverter->toNestedArray(
            $bannerimage,
            [],
            '\Custom\Chharo\Api\Data\BannerimageInterface'
        );
        return ConvertArray::toFlatArray($flatArray);
    }
}
