<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\ViewModel;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use SmartOSC\GroupOrder\Helper\Data;

class CartItemCustomer implements ArgumentInterface
{
    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerSession $customerSession
     * @param Data $helper
     */
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private CustomerSession $customerSession,
        private Data $helper
    ) {
    }

    /**
     * Check if module is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled(?int $storeId = null): bool
    {
        return $this->helper->isEnabled($storeId);
    }

    /**
     * Check if current customer is logged in (Frontend context)
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        return $this->customerSession->isLoggedIn();
    }

    /**
     * Get current logged-in customer ID (Frontend context)
     *
     * @return int
     */
    public function getCurrentCustomerId(): int
    {
        if (!$this->isEnabled()) {
            return 0;
        }

        return (int)$this->customerSession->getCustomerId();
    }

    /**
     * Get email for a customer
     *
     * @param int $customerId
     * @return string
     */
    public function getCustomerEmail(int $customerId): string
    {
        if (!$this->isEnabled() || !$customerId) {
            return '';
        }

        try {
            $customer = $this->customerRepository->getById($customerId);
            return (string)$customer->getEmail();
        } catch (NoSuchEntityException|LocalizedException $e) {
            return '';
        }
    }

    /**
     * Get combined customer info (Name & Email)
     *
     * @param int $customerId
     * @param bool $checkCurrent
     * @return string
     */
    public function getCustomerInfo(int $customerId, bool $checkCurrent = true): string
    {
        if (!$this->isEnabled()) {
            return '';
        }

        if (!$customerId) {
            return (string)__('Guest');
        }

        try {
            $customer = $this->customerRepository->getById($customerId);
            $name = $customer->getFirstname() . ' ' . $customer->getLastname();
            $email = $customer->getEmail();

            if ($checkCurrent && $customerId === $this->getCurrentCustomerId()) {
                $name .= ' (You)';
            }

            if ($name && $email) {
                return $name . ' (' . $email . ')';
            }

            return $name ?: ($email ?: '');
        } catch (NoSuchEntityException|LocalizedException $e) {
            return '';
        }
    }
}
