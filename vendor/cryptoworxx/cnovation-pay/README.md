CNovation-Pay API
=================
This is the repository for the official CNovation-Pay API.    
We plan to provide clients for different languages, for now starting with PHP.
There are also some plugins for well-known shop systems we will maintain.
Feel free to implement your own or contact us if you want a specific system
connected.

Installation
------------
API client is supposed to be usable as include only, so feel free to 
[grab a copy](https://raw.githubusercontent.com/Cryptoworxx/cnovation-pay/master/php/CNovationPayClient.php)
and add it to your source code.    
Additionally we support installation via composer (for PHP):
```
composer require cryptoworxx/cnovation-pay
```

Documentation
-------------
Find the API Reference over [here](reference.md).

Security and Authentication
---------------------------
The CNovation-Pay API uses SSL and only SSL to secure every request.
Every request needs a token argument to be accepted, API Token can be generated
in the [CNovation-Pay portal](https://www.c-novation-pay.com/portal/) or using
the APIs [Auth interface](https://www.c-novation-pay.com/api/authenticate).
Token can be unrecoverably deleted in your portal account at any time, so misuse can
be effectifely blocked.
API Token can optionally be registered for a specific system, so that 
they are only usable with an additional password.

Concepts
--------
Basically there are two ways of embedding the CNovation-pay API in your system:
* Use the API default user-interface
* Use the API to integrate in your own user-interface

For a sample of a complete integration see the [Shopware plugin](shopware/README.md) we maintain,
for a default-UI see the [WooCommerce plugin](wooCommerce/README.md).
