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
use Magento\Quote\Model\QuoteFactory;
use Psr\Log\LoggerInterface;

class Index extends Action implements HttpGetActionInterface
{
    private const TOKEN_PARAM = 'key';
    private const SUB_CATEGORY_PARAM = 'sub';

    /**
     * @param Context $context
     * @param CategoryRepositoryInterface $categoryRepository
     * @param QuoteFactory $quoteFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        private CategoryRepositoryInterface $categoryRepository,
        private QuoteFactory $quoteFactory,
        private LoggerInterface $logger
    ) {
        parent::__construct($context);
    }

    /**
     * Validate group order token and redirect to category page
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $groupOrderToken = (string)$this->getRequest()->getParam(self::TOKEN_PARAM);
        $subCategoryId = (int)$this->getRequest()->getParam(self::SUB_CATEGORY_PARAM);

        $quote = $this->quoteFactory->create()->load($groupOrderToken, 'order_cart_token');

        if (!$quote->getIsActive()) {
            $this->messageManager->addErrorMessage(
                __("You can't Group Order now because the URL link is out of date.")
            );

            return $resultRedirect->setPath('');
        }

        try {
            $subCategoryUrl = $this->categoryRepository->get($subCategoryId)->getUrl();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            throw new LocalizedException(__($e->getMessage()), $e);
        }

        $this->messageManager->addNoticeMessage(
            __('YOU CAN ONLY BUY ITEMS FROM THIS PAGE AND VIEW THE ITEMS IN THE MINICART.')
        );

        return $resultRedirect->setPath($subCategoryUrl . '?key=' . $groupOrderToken);
    }
}
