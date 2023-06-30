<?php
declare(strict_types=1);

namespace SmartOSC\GroupOrder\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartItemRepositoryInterface;

class DuplicateRowQuoteItem implements ObserverInterface
{
    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $cartRepository;
    private CartItemRepositoryInterface $cartItemRepository;

    /**
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        CartItemRepositoryInterface $cartItemRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->cartItemRepository = $cartItemRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quoteItemCurrent = $observer->getData('quote_item');
        $quoteId = $quoteItemCurrent->getQuoteId();
        $customerIdCurrent = $quoteItemCurrent->getCustomerId();

        $quoteItems = $this->cartItemRepository->getList($quoteId);

        $customerId = '';
        foreach ($quoteItems as $quoteItem) {
            $customerId = $quoteItem->getData('customer_id');
        }

        if ($customerIdCurrent !== null && (int)$customerIdCurrent !== $customerId) {
            $quoteItemCurrent->setId(null)
                ->setIsVirtual(0)
                ->setQty(0)
                ->setQuote($quoteItemCurrent->getQuote());
        }
    }
}
