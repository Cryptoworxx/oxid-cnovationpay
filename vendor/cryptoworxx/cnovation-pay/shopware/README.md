Some useful Links
=================
* [Simple Plugin guide (de)](https://www.the-cake-shop.de/shopware-plugin-101-einfaches-plugin-erstellen/)
* [Official plugin startup guide (en)](https://developers.shopware.com/developers-guide/plugin-quick-start/)
* [Developer Cheat sheet (en)](https://synonymous.rocks/shopware-5-cheat-sheet-fuer-entwickler/)
* [CNovation-Pay Demo-shop (de)](http://demo.c-novation-pay.com/shopware/)

Installation
============
Installing the plugin follows the standard procedure.    
This is namely:    
* upload the plugin ZIP file    
* install it    
* configure it    
* use it    
    
![upload plugin](upload_plugin.png)    
    
Once the plugin is uploaded and installed, you need to get an API token to
connect it to the CNovation-Pay ecosystem. You may generate a new token in your portal account
or you can simply click on the "Get Token" Button on the plugin config screen:    
![configure plugin](configure_plugin_1.png)    

An new windows opens and leads you to the process of [generating an API Token](../docs/get_token.md).    
    
Copy that token and paste it into the plugin config window:    
![configure plugin](configure_plugin_2.png)    
    
Now you can configure your Shopware installation to use the payment method 'C-Novation Pay'.    
![payment method](payment_method.png)    

Remarks
-------
The shopware plugin follows the "full integration" principle. It uses the
PHP-CNovationPayClient to separate the API calls from the Shop-UI.
There are some templates rendered into the Shopware Standard Theme that can be
overridden in custom themes.