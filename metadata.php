<?php

/**
 * Metadata version
 */
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
		\OxidEsales\Eshop\Core\ViewConfig::class                              => \Momentum\CNovationPay\Core\ViewConfig::class,
		\OxidEsales\Eshop\Application\Controller\PaymentController::class     => \Momentum\CNovationPay\Controller\PaymentController::class,
		\OxidEsales\Eshop\Application\Controller\OrderController::class       => \Momentum\CNovationPay\Controller\OrderController::class,
//    	\OxidEsales\Eshop\Application\Controller\StartController::class => \Momentum\CNovationPay\Controller\StartController::class,
//		\OxidEsales\Eshop\Application\Controller\PaymentController::class     => Momentum\CNovationPay\Controller\PaymentController::class,
    ],
	'events'      => [
		'onActivate'   => '\Momentum\CNovationPay\Core\Events::onActivate',
//		'onActivate'   => '\OxidEsales\PayPalModule\Core\Events::onActivate',
		'onDeactivate' => '\Momentum\CNovationPay\Core\Events::onDeactivate'
	],
    'controllers' => [
    	'mmcnovationpaystandarddispatcher'      => \Momentum\CNovationPay\Controller\StandartDispatcher::class,
		'CNovationCheckout'                     => \Momentum\CNovationPay\Controller\CNovationCheckoutController::class,

	],
    'templates'   => [
		'mmcnovationpay_checkout.tpl'        => 'momentum/cnovationpay/views/tpl/checkout/mmcnovationpay_checkout.tpl',
	],
    'blocks'      => [

	],
    'settings'    => [
		array('group' => 'cnovation_api', 'name' => 'sCPApiEndpoint',      'type' => 'str',   'value' => 'https://www.c-novation-pay.com/api'),
		array('group' => 'cnovation_api', 'name' => 'sCPApiToken',      'type' => 'str',   'value' => ''),
	]
);