<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\GroupOrder\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;

class EmailCC implements ObserverInterface
{
    /**
     * @var Session
     */
    protected Session $checkoutSession;
    /**
     * @var CartItemRepositoryInterface
     */
    private CartItemRepositoryInterface $cartItemRepository;
    /**
     * @var CustomerCollectionFactory
     */
    private CustomerCollectionFactory $customerCollectionFactory;

    /**
     * @param Session $checkoutSession
     * @param CartItemRepositoryInterface $cartItemRepository
     * @param CustomerCollectionFactory $customerCollectionFactory
     */
    public function __construct(
        Session $checkoutSession,
        CartItemRepositoryInterface $cartItemRepository,
        CustomerCollectionFactory $customerCollectionFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->cartItemRepository = $cartItemRepository;
        $this->customerCollectionFactory = $customerCollectionFactory;
    }

    /**
     * Observer setEmailCc
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quoteId = $order->getQuoteId();

        if (!$quoteId) {
            return;
        }

        $quoteItems = $this->cartItemRepository->getList($quoteId);
        $emailList = [];
        $customerIds = [];

        foreach ($quoteItems as $quoteItem) {
            $customerId = $quoteItem->getData('customer_id');
            $customerIds[] = $customerId;
        }

        $customerCollection = $this->customerCollectionFactory->create();
        $customerCollection->addFieldToFilter('entity_id', ['in' => $customerIds]);

        foreach ($customerCollection as $customer) {
            $emailList[] = $customer->getEmail();
        }

        if (count($emailList)) {
            $this->checkoutSession->setEmailCc(json_encode($emailList));
        }
    }
}
