<?php

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

/**
 * Class Handler
 * @package SmartOSC\GroupOrder\Logger
 */
class Handler extends Base
{
    /**
     * @var string
     */
    protected $fileName = 'var/log/group_order/group_order.log';

    /**
     * @var int
     */
    protected $loggerType = Logger::DEBUG;

    /**
     * Handler constructor.
     *
     * @param \Magento\Framework\Filesystem\DriverInterface $filesystem
     * @param string|null $filePath
     * @param string|null $fileName
     */
    public function __construct(
        \Magento\Framework\Filesystem\DriverInterface $filesystem,
        $filePath = null,
        $fileName = null
    ) {
        $date = new \DateTime();
        $this->fileName = 'var/log/group_order/group_order_' . $date->format('Y-m-d') . '.log';
        parent::__construct($filesystem, $filePath, $fileName);
    }
}
