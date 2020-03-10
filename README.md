# Omnipay - Paymongo

A Paymongo driver for [Omnipay](https://omnipay.thephpleague.com/) PHP payment processing library.

## Installation

```
composer require omarusman/omnipay-paymongo
```

## Usage

### Credit or Debit Card

To start processing payment via Paymongo's Credit or Debit Card.

**1. Make an Omnipay Gateway:**
```php
$gateway = Omnipay::create('Paymongo_Card');
$gateway->setKeys('pk_test_csp6bBgoLuJ6TXT6Nzm6bTVN', 'sk_test_NW1bkhC8pa77ttiYQaJcVAhU');
```
This will create you a new instance of Omnipay Paymongo_Card gateway
and set your Paymongo's `public key` and `secret key`.

You can view your keys at [Paymongo Developer Page](https://dashboard.paymongo.com/developers)

**2. Authorize a credit or debit card:**
```php
$token = $gateway->authorize([
    'number'      => '4123 4501 3100 0508',
    'expiryMonth' => '1',
    'expiryYear'  => '22',
    'cvv'         => '123',
]);
```
This will return a `Token` object which you can use to process a payment.

**3. Process a Payment**
```php
$payment = $gateway->purchase([
    'amount'               => '123.45',
    'currency'             => 'PHP',
    'description'          => 'Just a purchase!',
    'statement_descriptor' => 'MyCo',
    'token'                => $token,
]);
```
This will return a `Payment` object containing information about your payment.
