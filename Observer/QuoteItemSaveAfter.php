<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Observer;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\ResourceModel\Quote\Item as QuoteItemResourceModel;

class QuoteItemSaveAfter implements ObserverInterface
{
    /**
     * @var CustomerSession
     */
    protected CustomerSession $customerSession;

    /**
     * @var QuoteItemResourceModel
     */
    private QuoteItemResourceModel $quoteItemResourceModel;

    /**
     * @param CustomerSession $customerSession
     * @param QuoteItemResourceModel $quoteItemResourceModel
     */
    public function __construct(
        CustomerSession $customerSession,
        QuoteItemResourceModel $quoteItemResourceModel
    ) {
        $this->customerSession = $customerSession;
        $this->quoteItemResourceModel = $quoteItemResourceModel;
    }

    /**
     * Event save set name customer to quote_item
     *
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $quoteItem = $observer->getEvent()->getItem();
        $customerSession = $this->customerSession->getCustomer();

        if ($customerSession->getId()) {
            $customerName = $customerSession->getName();
            $quoteItem->setData('name_customer_add_to_cart', $customerName);

            $this->quoteItemResourceModel->save($quoteItem);
        }
    }
}
