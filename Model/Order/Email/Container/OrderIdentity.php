<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\GroupOrder\Model\Order\Email\Container;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session;

class OrderIdentity extends \Magento\Sales\Model\Order\Email\Container\OrderIdentity
{
    /**
     * @var Session
     */
    private Session $checkoutSession;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param Session $checkoutSession
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Session $checkoutSession
    ) {
        parent::__construct($scopeConfig, $storeManager);
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Override getEmailCopyTo
     *
     * @return array|bool|void
     */
    public function getEmailCopyTo()
    {
        $emailCC = $this->checkoutSession->getEmailCc();
        $data = $this->getConfigValue(self::XML_PATH_EMAIL_COPY_TO, $this->getStore()->getStoreId());

        if (!empty($data) && !empty($emailCC)) {
            $emailList = array_merge(explode(',', $data), json_decode($emailCC, true));
        } elseif (empty($data) && !empty($emailCC)) {
            $emailList = json_decode($emailCC, true);
        } elseif (!empty($data) && empty($emailCC)) {
            $emailList = explode(',', $data);
        } else {
            return false;
        }

        return array_map('trim', $emailList);
    }
}
