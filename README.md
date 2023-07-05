# Magento 2 Group Order extension

Group Order Extension helps customers in ordering their shopping cart with friends and family as well. This is a supportive method to promote store’s conversion rate via the existing users, and this can significantly contribute to the revenue of the store.

- Many people can order the same cart
- Share subcategory as you like
- Share link Group Order quickly
- Update cart share current quickly

## 1. Documentation

- [Contribute on Github](https://github.com/gnocman/magento2-group-order)

## 2. FAQs

**Q: How can customers use share button?**

A: Customers only need to click on the button and paste the automated URL to anywhere they want to share.

**Q: Where will the Share button appear on the website?**

A: Share button can be seen on **Subcategory** page.

## 3. How to install Group Order extension for Magento 2

- Install via composer (recommend)

Run the following command in Magento 2 root folder:
```
composer require smartosc/module-group-order
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy -f
```

## 4. Highlight Features

### Quick share by copy-and-paste

**Group Order Extension** allows the store owners to add an extra button which is **Group Order** while a customer is processing their purchasing.

The button can be displayed in the **Subcategory** section. By clicking this button, the customer can copy their shopping cart’s URL and paste to a destination just in the blink of an eye. When the URL recipient clicks on the shared URL, they can shop at the current subcategory that the person who shared the url.

![Quick share by copy-and-paste](https://github.com/gnocman/m246/assets/55309917/2ae3c4bf-a15d-4875-a69b-96800b119867)

## 5. More features

### Update function

**Update Shopping Cart** button is for updating the shopping cart with the latest changes from the original cart.

### Mobile responsive ability

The module is properly responsive with both mobile and desktop devices.

## 6. Full Magento 2 Group Order Features

### For store owners
- Enable/disable the extension
- View Order Information

### For customers
- Quickly and easily share the shopping cart
- Briefly view the shared shopping cart
- View Order Information

## 7. How to configure Group Order in Magento 2

### 7.1 Configuration

- Access to your Magento 2 Admin Panel, navigate to `Store tab > Open Settings > Configuration `
- Click `General > Group Order Extention`, go to `General Configuration` section.

![Magento 2 Group Order extension configure](https://github.com/gnocman/m246/assets/55309917/aba4df53-26b8-4cf2-97cd-fabfd80df2c6)

#### 7.1.1. General

- **Enable**: Select `Yes` to enable the extension
```
php bin/magento c:f
```

### 7.2 Frontend
**IMPORTANT NOTE:** All customers who want to Group Order need to login

After activating the module, customers can use **Group Order** button to deliver the URL to people which they want to share the cart. After sharing, url will return the Subcategory that was shared by the customer.

- **Initiate A Group Order On This Page.** button displays in the **Subcategory** section when adding items to cart.

![Magento 2 Group Order module](https://github.com/gnocman/m246/assets/55309917/2ae3c4bf-a15d-4875-a69b-96800b119867)

**Example Url When Click Button: https://example.local.com/grouporder/index/index/key/I1GUnk8KMuMMY31/sub/4/**

- **After the customer clicks on the shared link, the interface will look like this**

![Magento 2 Group Order module](https://github.com/gnocman/m246/assets/55309917/32a4e5c2-0036-423e-88fa-ee223ea9d9f5)

**Example Url After Click Button: https://example.local.com/test.html?key=I1GUnk8KMuMMY31**

- When adding products to the cart successfully, we have 2 places to view the Group Order cart.

![Magento 2 Group Order module](https://github.com/gnocman/m246/assets/55309917/09e04f51-167e-4d9a-a8b4-c46fc0d3076b)

![Magento 2 Group Order module](https://github.com/gnocman/m246/assets/55309917/eabcb0fb-5fcf-4319-aeca-84d6f94e0589)

- To see what items the cart has added, in **Minicart** we click the **View Cart Group Order** or click **shopping cart** in the frontend button and can see the names of people who have added items to the Group Order

![View Cart  Group Order](https://github.com/gnocman/m246/assets/55309917/f8b01994-72b4-4709-b540-2ef50c79bacf)

- After the customer Shares the order success link, the order information email will be sent to everyone who purchases in the Group Order

![View Cart  Group Order](https://github.com/gnocman/m246/assets/55309917/0ce6b965-e431-4775-bb44-60b1ea055a82)

- Customer can see the customer's name add items to cart in My Order.

![View My Order Group Order](https://github.com/gnocman/m246/assets/55309917/199c1606-501a-40ba-b5e5-431939110b94)

- Admin can see the customer's name add items to cart in Order Detail Page.

![View My Order Group Order](https://github.com/gnocman/m246/assets/55309917/1095a024-a4a4-4d58-9ad5-a654b0e832a0)
