# Simple captcha module

## Description

This module provides a simple captcha ("Completely Automated Public Turing test to tell Computers and Humans Apart")
challenge (distorted characters in an image).

It is used to ensure that only a user who can read the distorted characters and enter them in the related input field
can submit the following forms:

 - contact
 - invite
 - pricealarm
 - suggest
 - forgot password
 - newsletter
 - register
 - suggest

The captcha module then validates the submitted value against the expected one and then decides whether to process the
request (e.g. send contact mail to shop administrator) or refuse and show an error message instead.

## Installation

Please proceed the following way to install the module:

### Module installation via composer

In order to install the module via composer, run the following commands in commandline of your shop base directory 
(where the shop's composer.json file resides).

```
composer require o3-shop/simple-captcha
```

## Activate Module

- Activate the module in the administration panel.

## Uninstall

Disable the module in administration area and remove it from installation:

```
composer remove o3-shop/simple-captcha
```
