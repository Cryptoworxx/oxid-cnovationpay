Quickstart
==========
* Log into portal and [create an API token](https://www.c-novation-pay.com/portal/apitoken/)
* Use the API by posting data to `https://www.c-novation-pay.com/api/<yourtoken>/<controller>/<arguments>`
* Response will be JSON Object with these keys: success, code, message, info, result (where result is the actual result)


Controllers
===========
* [register](#register)
* [currencies](#currencies)
* [wallets](#wallets)
* [payments](#payments)
* [payments/create](#payments_create)
* [payments/cancel](#payments_cancel)
* [payments/delete](#payments_delete)


<a id="register"></a>
Controller: register
====================
You can use the API token to access the API at any time. More security is optionally
available when you register the Token exclusively for your system.
Once this is done you will need to verify each API call with an additional parameter named 'secret'.
registration is done by calling the register controller like this:
```
URL: https://www.c-novation-pay.com/api/<yourtoken>/register?identifier=<identify the system>&type=<type>
Response:
{
	"success": true,
	"result": {
		"secret": "<secret_code>"
	}
}
```
Identifier and type can be any string, type will be used to provide an icon like for the Shopware addon or POS devices.


<a id="currencies"></a>
Controller: currencies
======================
Returns a list of supported currencies.
```
URL: https://www.c-novation-pay.com/api/<yourtoken>/currencies
Response:
{
	"success": true,
	"result": [
		{
			"code": "ETH",
			"name": "Ethereum",
			"exchangerates": {
				"BTC": 0.02973276,
				"CHF": 157.9218498626,
				"DASH": 1.42177472,
				"ETC": 27.73969734,
				"EUR": 138.92863791585,
				"LTC": 2.258783,
				"MXN": 2947.9559429504,
				"USD": 158.47684732499,
				"XMR": 2.55727935,
				"XRP": 529.01935682,
				"ZEC": 2.62828467
			},
			"icon": "https://www.c-novation-pay.com/api/currencies/svg/ETH.svg"
		},
		{
			"code": "BTC",
			"name": "Bitcoin",
			"exchangerates": {
				"CHF": 5368.4329838481,
				"DASH": 47.49282977,
				"ETC": 931.79276929,
				"ETH": 33.632935522972,
				"EUR": 4722.7732124303,
				"LTC": 75.99841011,
				"MXN": 100213.51657693,
				"USD": 5304.4769785699,
				"XMR": 86.580236502574,
				"XRP": 17787.26431875,
				"ZEC": 87.85339026
			},
			"icon": "https://www.c-novation-pay.com/api/currencies/svg/BTC.svg"
		}
	]
}
```
Get details for one Ethereum
```
URL: https://www.c-novation-pay.com/api/<yourtoken>/currencies?code=ETH
Response:
{
	"success": true,
	"result": {
		"code": "ETH",
		"name": "Ethereum",
		"exchangerates": {
			"BTC": 0.02973276,
			"CHF": 157.9218498626,
			"DASH": 1.42177472,
			"ETC": 27.73969734,
			"EUR": 138.92863791585,
			"LTC": 2.258783,
			"MXN": 2947.9559429504,
			"USD": 158.47684732499,
			"XMR": 2.55727935,
			"XRP": 529.01935682,
			"ZEC": 2.62828467
		},
		"icon": "https://www.c-novation-pay.com/api/currencies/svg/ETH.svg"
	}
}
```


<a id="wallets"></a>
Controller: wallets
===================
Lists all your wallets or returns details for a specific wallet.
```
URL: https://www.c-novation-pay.com/api/<yourtoken>/wallets
Response:
{
	"success": true,
	"result": {
		"paging": {
			"rows_per_page": 20,
			"current_page": 1,
			"total_pages": 1,
			"total_rows": 4,
			"offset": 0
		},
		"items": [
			{
				"uid": "<wallet_uid>",
				"currency": {
					"code": "ETH",
					"name": "Ethereum"
				},
				"name": "My Wallet",
				"balance": 0,
				"code": "0x49...fb"
			},
			...
		]
	}
}
```
Get one wallet
```
URL: https://www.c-novation-pay.com/api/<yourtoken>/wallets?uid=<wallet_uid>
Response:
{
	"success": true,
	"result": {
		"uid": "<wallet_uid>",
		"currency": {
			"code": "ETH",
			"name": "Ethereum"
		},
		"user": {
			... user details
		},
		"name": "My Wallet",
		"balance": 0,
		"code": "0x49...fb",
	}
}
```


<a id="payments"></a>
Controller: payments
====================
Lists all payments.
```
URL: https://www.c-novation-pay.com/api/<yourtoken>/payments
Response:
{
	"success": true,
	"result": {
		"paging": {
			"rows_per_page": 20,
			"current_page": 1,
			"total_pages": 1,
			"total_rows": 10,
			"offset": 0
		},
		"items": [{
			"uid": "<payment_uid>",
			"deadline": "2018-11-06T15:41:46+01:00",
			"cancelled": "2018-11-06T15:37:46+01:00",
			"price": 0.02665345,
			"requested_price": 5,
			"reference": "20001",
			"cancel_reason": "",
			"status": "cancelled|others"
		},
		.....]
	}
}
```
Get one payment:
```
URL: https://www.c-novation-pay.com/api/<yourtoken>/payments?uid=<payment_uid>
Response:
{
	"success": true,
	"result": {
		"uid": "<payment_uid>",
		"deadline": "2019-01-15T11:49:27+01:00",
		"cancelled": "2019-01-15T11:50:01+01:00",
		"currency": {
			"code": "ETH",
			"name": "Ethereum"
		},
		"user": {
			... users details
		},
		"wallet": {
			... target wallet details
		},
		"price": 3.2350105361,
		"requested_price": 359.99,
		"reference": "20002",
		"cancel_reason": "Timeout",
		"status": "cancelled",
		"requested_currency": {
			"code": "EUR",
			"name": "Euro"
		},
		"transactions": [],
		"external": true
	}
}
```


<a id="payments_create"></a>
Controller: payments/create
===========================
Creates a payment.
```
URL: https://www.c-novation-pay.com/api/<yourtoken>/payments/create?currency=<CryptoCurrencyCode>&price=<price>&price_currency=<CurrencyThePriceIsGivenIn>&reference=<somereference>&callback=<optional_callback_url>
Response:
{
	"success": true,
	"result": {
		"uid": "<payment_uid>",
		"deadline": "2018-11-28T16:14:04+01:00",
		"price": 0.0952921,
		"requested_price": 10,
		"reference": "myordernumber1",
		"callback": "",
		"status": "pending",
		"currency": {
			"code": "ETH",
			"name": "Ethereum"
		},
		"wallet": "0xe9d6c1ed4bd0ad5eea2sdfsdff69ac12e80baf9f029",
		"urls": {
			"transaction": "ethereum:0xe9d6c1ed4bd0ad5eea2sdfsdff69ac12e80baf9f029?value=0.0952921",
			"qrcode": "https:\/\/www.c-novation-pay.com\/api\/qr\/<payment_uid>.svg",
			"pay": "https:\/\/www.c-novation-pay.com\/api\/pay\/<payment_uid>",
			"cancel": "https:\/\/www.c-novation-pay.com\/api\/pay\/cancel\/<payment_uid>"
		}
	}
}
```
<optional_callback_url> defines an address where the CNovation-Pay API posts status updates about the payment, for example once
the payment arrives.


<a id="payments_create"></a>
Controller: payments/checkout
=============================
Initialises a checkout process.
```
URL: https://www.c-novation-pay.com/api/poTdGhb01CgvnnTufGp1T2e2b/payments/checkout?ident=<your_ident>&price=<price>&price_currency=<fiat_currency_code>&reference=<reference>&return_url=<your_return_url>[&callback=<optional_callback_url>][&cancel_url=<optional_cancel_url>]
Response:
{
	"success": true,
	"result": {
		"ident": "0001",
		"checkout_uid": "<checkout_uid>",
		"checkout_url": "https://www.c-novation-pay.com/api/pay/checkout/<checkout_uid>"
	}
}
```
To start the checkout you must redirect the users browser to the `<checkout_url>`given in 
the response object. The browser will be redirected back to your system when the payment process
is completed (Note: that does not mean, that the money already arrived!).
If a `<cancel_url>` is specified, browser will be redirected there when the payment is cancelled for 
any reason. If a `<callback>` is specified it will be polled at any status change for the payment.


<a id="payments_cancel"></a>
Controller: payments/cancel
===========================
Cancels a payment.
```
URL: https://www.c-novation-pay.com/api/<yourtoken>/payments/cancel?uid=<payment_uid>&reason=<optional cancel reason>
Response:
{
	"success": true,
	"result": {
		"uid": "<payment_uid>",
		"deadline": "2018-11-28T16:14:04+01:00",
		"cancelled": "now()",
		"price": 0.0952921,
		"requested_price": 10,
		"reference": "myordernumber1",
		"cancel_reason": "my reason",
		"callback": "",
		"status": "cancelled"
	}
}
```


<a id="payments_delete"></a>
Controller: payments/delete
===========================
Deletes a payment.
```
URL: https://www.c-novation-pay.com/api/<yourtoken>/payments/delete?uid=<payment_uid>
Response:
{
	"success":true
}
```

