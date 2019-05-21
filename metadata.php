<?php

/**
 * Metadata version
 */

use Momentum\CNovationPay\Controller\CNovationCheckoutController;
use Momentum\CNovationPay\Controller\StandartDispatcher;
use Momentum\CNovationPay\Model\CNovationPayOrder;
use OxidEsales\Eshop\Application\Controller\OrderController;
use OxidEsales\Eshop\Application\Controller\PaymentController;
use OxidEsales\Eshop\Core\ViewConfig;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Controller\ThankYouController;

$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = array(
    'id'          => 'momentumc-novationpay',
    'title'       => 'MOMENTUM :: C-Novation-Pay',
    'description' => [
    	'de'	=> 'Akzeptieren Sie Kryptowährungen, wie Bitcoin und Ethereum als zusätzliche Zahlungsmöglichkeit.',
		'en'	=> 'Accept payment methods in cryptocurrency like bitcoin or ethereum.'

	],
    'thumbnail'   => 'out/pictures/picture.png',
    'version'     => '0.0.1-DEV',
    'author'      => 'Scavix Software GmbH & Co. KG',
    'url'         => 'https://www.scavix.com',
    'email'       => 'info@scavix.com',
    'extend'      => [
		ViewConfig::class           => \Momentum\CNovationPay\Core\ViewConfig::class,
		PaymentController::class    => \Momentum\CNovationPay\Controller\PaymentController::class,
		OrderController::class      => \Momentum\CNovationPay\Controller\OrderController::class,
        ThankYouController::class   => \Momentum\CNovationPay\Controller\ThankYouController::class,
        \OxidEsales\Eshop\Application\Model\Order::class               => CNovationPayOrder::class,
    ],
	'events'      => [
		'onActivate'   => '\Momentum\CNovationPay\Core\Events::onActivate',
//		'onActivate'   => '\OxidEsales\PayPalModule\Core\Events::onActivate',
		'onDeactivate' => '\Momentum\CNovationPay\Core\Events::onDeactivate'
	],
    'controllers' => [
    	'mmcnovationpaystandarddispatcher'      => StandartDispatcher::class,
		'CNovationCheckout'                     => CNovationCheckoutController::class,

	],
    'templates'   => [
		'mmcnovationpay_checkout.tpl'        => 'momentum/cnovationpay/views/tpl/checkout/mmcnovationpay_checkout.tpl',
		'mmcnovationpay_payment.tpl'        => 'momentum/cnovationpay/views/tpl/checkout/mmcnovationpay_payment.tpl',
	],
    'blocks'      => [
        [
            'template'  => 'page/checkout/thankyou.tpl',
            'block'     => 'checkout_thankyou_info',
            'file'      => 'views/blocks/page/checkout/thankyou_checkout_thankyou_info.tpl'
        ]
	],
    'settings'    => [
		array('group' => 'cnovation_api', 'name' => 'sCPApiEndpoint',      'type' => 'str',   'value' => 'https://www.c-novation-pay.com/api'),
		array('group' => 'cnovation_api', 'name' => 'sCPApiToken',      'type' => 'str',   'value' => ''),
	]
);