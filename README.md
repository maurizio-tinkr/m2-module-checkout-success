# Test the Magento 2 Checkout Success Page

    ``tinkr/m2-module-checkout-success``

- [Main Functionalities](#markdown-header-main-functionalities)
- [Installation](#markdown-header-installation)
- [Configuration](#markdown-header-configuration)
- [Specifications](#markdown-header-specifications)


## Main Functionalities
Module to test the Magento 2 checkout success page

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

- Unzip the zip file in `app/code/Tinkr`
- Enable the module by running `php bin/magento module:enable Tinkr_CheckoutSuccess`
- Apply database updates by running `php bin/magento setup:upgrade`\*
- Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

- Install the module composer by running:
  
  `composer require --dev tinkr/m2-module-checkout-success`
- enable the module by running `php bin/magento module:enable Tinkr_CheckoutSuccess`
- apply database updates by running `php bin/magento setup:upgrade`\*
- Flush the cache by running `php bin/magento cache:flush`


## Configuration

- Order ID (checkout_success/general/order_id)

- Enable (checkout_success/general/enabled)


## Specifications

- Controller
    - frontend > test/checkout/success

An order ID is necessary for the page to work, this can be set via the Admin configuration or passed as a URL parameter