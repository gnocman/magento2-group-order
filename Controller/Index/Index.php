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

class Index extends Action implements HttpGetActionInterface
{
    /**
     * @var CategoryRepositoryInterface
     */
    private CategoryRepositoryInterface $categoryRepository;

    /**
     * @param Context $context
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Context $context,
        CategoryRepositoryInterface $categoryRepository
    ) {
        parent::__construct($context);
        $this->categoryRepository = $categoryRepository;
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

        try {
            $subCategoryIdUrl = $this->categoryRepository->get($subCategoryId)->getUrl();
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()), $e);
        }

        $url = $subCategoryIdUrl . '?key=' . $groupOrderToken;
        $this->messageManager->addNoticeMessage(
            'YOU CAN ONLY BUY ITEM FROM THIS PAGE AND VIEW THE ITEM IN THE MINICART.'
        );

        return $resultRedirect->setPath($url);
    }
}
