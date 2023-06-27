<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    public const CONFIG_MODULE_PATH = 'sharecart/general/enabled';

    /**
     * Get isEnabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::CONFIG_MODULE_PATH, ScopeInterface::SCOPE_STORE);
    }
}
