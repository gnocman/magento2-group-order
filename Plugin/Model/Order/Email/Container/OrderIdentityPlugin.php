<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Plugin\Model\Order\Email\Container;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use SmartOSC\GroupOrder\Helper\Data;

class OrderIdentityPlugin
{
    /**
     * @param CheckoutSession $checkoutSession
     * @param Data $helper
     */
    public function __construct(
        private CheckoutSession $checkoutSession,
        private Data $helper
    ) {
    }

    /**
     * Merge Group Order emails into Order CC list
     *
     * @param OrderIdentity $subject
     * @param array|false $result
     * @return array|false
     */
    public function afterGetEmailCopyTo(OrderIdentity $subject, $result)
    {
        if (!$this->helper->isEnabled()) {
            return $result;
        }

        $emailCC = $this->checkoutSession->getEmailCc();
        if (empty($emailCC)) {
            return $result;
        }

        $sessionEmails = json_decode($emailCC, true) ?? [];
        if (empty($sessionEmails)) {
            return $result;
        }

        $resultEmails = $result !== false ? (is_array($result) ? $result : explode(',', (string)$result)) : [];
        $emailList = array_merge($resultEmails, $sessionEmails);
        $emailList = array_map('trim', $emailList);
        $emailList = array_unique(array_filter($emailList));

        return empty($emailList) ? false : $emailList;
    }
}
