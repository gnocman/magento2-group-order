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

class CartItemCustomer implements ArgumentInterface
{
    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerSession $customerSession
     */
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private CustomerSession $customerSession
    ) {
    }

    /**
     * Check if current customer is logged in (Frontend context)
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * Get current logged-in customer ID (Frontend context)
     *
     * @return int
     */
    public function getCurrentCustomerId(): int
    {
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
        if (!$customerId) {
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
        if (!$customerId) {
            return '';
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
