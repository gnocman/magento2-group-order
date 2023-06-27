<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SmartOSC\GroupOrder\Plugin\Checkout\Helper;

use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\RequestInterface;

class AddParamCheckoutCart
{
    /**
     * @var UrlInterface
     */
    private UrlInterface $url;
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * AddParamCheckoutCart constructor.
     *
     * @param UrlInterface $url
     * @param RequestInterface $request
     */
    public function __construct(
        UrlInterface $url,
        RequestInterface $request
    ) {
        $this->url = $url;
        $this->request = $request;
    }

    /**
     * Add custom parameter to the add to cart URL
     *
     * @param CartHelper $subject
     * @param string $result
     * @return string
     */
    public function afterGetAddUrl(CartHelper $subject, $result)
    {
        $params = $this->request->getParam('key');

        if ($params) {
            return $this->url->getUrl('checkout/cart/add', ['key' => $params]);
        }

        return $result;
    }
}
