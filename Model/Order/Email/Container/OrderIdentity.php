<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Model\Order\Email\Container;

use Magento\App\Config\ScopeConfigInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Config\ScopeConfigInterface as FrameworkScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use SmartOSC\GroupOrder\Helper\Data;

class OrderIdentity extends \Magento\Sales\Model\Order\Email\Container\OrderIdentity
{
    /**
     * @param FrameworkScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param CheckoutSession $checkoutSession
     * @param Data $helper
     */
    public function __construct(
        FrameworkScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        private CheckoutSession $checkoutSession,
        private Data $helper
    ) {
        parent::__construct($scopeConfig, $storeManager);
    }

    /**
     * Return merged list of CC email recipients including group order participants
     *
     * @return array|false
     */
    public function getEmailCopyTo()
    {
        if (!$this->helper->isEnabled()) {
            return parent::getEmailCopyTo();
        }

        $emailCC = $this->checkoutSession->getEmailCc();
        $configData = $this->getConfigValue(self::XML_PATH_EMAIL_COPY_TO, $this->getStore()->getStoreId());

        $configEmails = !empty($configData) ? explode(',', $configData) : [];
        $sessionEmails = !empty($emailCC) ? json_decode($emailCC, true) ?? [] : [];

        $emailList = array_merge($configEmails, $sessionEmails);

        if (empty($emailList)) {
            return false;
        }

        return array_map('trim', $emailList);
    }
}
