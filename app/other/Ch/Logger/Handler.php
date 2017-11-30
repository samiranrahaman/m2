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
namespace Custom\Chharo\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level.
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     *
     * @var int
     */
    protected $loggerType = ChharoLogger::DEBUG;

    /**
     * File name.
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     *
     * @var string
     */
    protected $fileName = '/var/log/chharo.log';
}
