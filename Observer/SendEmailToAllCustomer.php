<?php

namespace SmartOSC\GroupOrder\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class SendEmailToAllCustomer implements ObserverInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;
    /**
     * @var CartItemRepositoryInterface
     */
    private CartItemRepositoryInterface $quoteItemRepository;
    /**
     * @var TransportBuilder
     */
    private TransportBuilder $transportBuilder;
    /**
     * @var StateInterface
     */
    private StateInterface $inlineTranslation;
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param CartItemRepositoryInterface $quoteItemRepository
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CartItemRepositoryInterface $quoteItemRepository,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->customerRepository = $customerRepository;
        $this->quoteItemRepository = $quoteItemRepository;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quoteId = $order->getQuoteId();
        $emailCurrentCustomer = $order->getCustomerEmail();

        if ($quoteId) {
            $quoteItems = $this->quoteItemRepository->getList($quoteId);
            foreach ($quoteItems as $quoteItem) {
                $customerId = $quoteItem->getData('customer_id');
                $customer = $this->customerRepository->getById($customerId);

                $emailCustomer = $customer->getEmail();
                $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();

                if ($emailCustomer !== $emailCurrentCustomer) {
                    $this->sendEmailToCustomer($emailCustomer, $customerName, $order);
                }
            }
        }
    }

    /**
     * @param string $email
     * @param string $customerName
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function sendEmailToCustomer($email, $customerName, $order)
    {
        $templateOptions = [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $this->storeManager->getStore()->getId()
        ];

        $templateVars = [
            'name' => $customerName,
            'email' => $email,
            'is_notified' => true,
            'order_id' => $order->getId(),
            'store' => $this->storeManager->getStore(),
            'order' => $order,
        ];

        $from = [
            'email' => $this->scopeConfig->getValue(
                'trans_email/ident_general/email',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ),
            'name' => $this->scopeConfig->getValue(
                'trans_email/ident_general/name',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        ];

        $this->inlineTranslation->suspend();

        $transport = $this->transportBuilder
            ->setTemplateIdentifier('sales_email_order_template')
            ->setTemplateOptions($templateOptions)
            ->setTemplateVars($templateVars)
            ->setFromByScope($from)
            ->addTo($email)
            ->getTransport();

        $transport->sendMessage();

        $this->inlineTranslation->resume();
    }
}
