<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Plugin\Checkout\Helper;

use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;

class AddParamCheckoutCart
{
    /**
     * @param UrlInterface $url
     * @param RequestInterface $request
     */
    public function __construct(
        private UrlInterface $url,
        private RequestInterface $request
    ) {
    }

    /**
     * Append group order token to add-to-cart URL when in group order context
     *
     * @param CartHelper $subject
     * @param string $result
     * @return string
     */
    public function afterGetAddUrl(CartHelper $subject, string $result): string
    {
        $token = (string)$this->request->getParam('key');

        if ($token) {
            return $this->url->getUrl('checkout/cart/add', ['key' => $token]);
        }

        return $result;
    }
}
