/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'mage/translate',
    'mage/url'
], function ($, Component, customerData, $t, url) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'SmartOSC_GroupOrder/redirect-button'
        },

        isLoggedIn: function () {
            let customer = customerData.get('customer');
            return customer().firstname !== undefined && this.hasToken();
        },

        hasToken: function () {
            let currentUrl = window.location.href;
            let token = this.extractTokenFromUrl(currentUrl);
            return token !== null;
        },

        redirect: function () {
            let currentUrl = window.location.href;
            let token = this.extractTokenFromUrl(currentUrl);
            let controllerUrl = url.build('sharecart/cart/index');
            window.open(controllerUrl + (token ? '?key=' + encodeURIComponent(token) : ''));
        },

        extractTokenFromUrl: function (url) {
            let urlObject = new URL(url);
            let searchParams = new URLSearchParams(urlObject.search);
            return searchParams.get('key');
        }
    });
});
