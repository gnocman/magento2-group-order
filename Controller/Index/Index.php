<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace SmartOSC\GroupOrder\Controller\Index;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\QuoteFactory;

class Index extends Action implements HttpGetActionInterface
{
    /**
     * @var CategoryRepositoryInterface
     */
    private CategoryRepositoryInterface $categoryRepository;
    /**
     * @var QuoteFactory
     */
    private QuoteFactory $quoteFactory;

    /**
     * @param Context $context
     * @param CategoryRepositoryInterface $categoryRepository
     * @param QuoteFactory $quoteFactory
     */
    public function __construct(
        Context $context,
        CategoryRepositoryInterface $categoryRepository,
        QuoteFactory $quoteFactory
    ) {
        parent::__construct($context);
        $this->categoryRepository = $categoryRepository;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * Controller redirect to categories
     *
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $groupOrderToken = $this->getRequest()->getParam('key');
        $subCategoryId = $this->getRequest()->getParam('sub');

        $quote = $this->quoteFactory->create()->load($groupOrderToken, 'order_cart_token');
        $isActive = $quote->getIsActive();

        try {
            $subCategoryIdUrl = $this->categoryRepository->get($subCategoryId)->getUrl();
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()), $e);
        }

        $url = $subCategoryIdUrl . '?key=' . $groupOrderToken;

        if (empty($isActive)) {
            $this->messageManager->addErrorMessage(
                'You can\'t Group Order now because the URL link is out of date.'
            );

            return $resultRedirect->setPath('');
        }

        $this->messageManager->addNoticeMessage(
            'YOU CAN ONLY BUY ITEMS FROM THIS PAGE AND VIEW THE ITEMS IN THE MINICART.'
        );

        return $resultRedirect->setPath($url);
    }
}
