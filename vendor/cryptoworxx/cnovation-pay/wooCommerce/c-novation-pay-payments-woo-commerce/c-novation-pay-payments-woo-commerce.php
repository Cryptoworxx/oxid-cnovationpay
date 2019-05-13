<?php
/*
 * Plugin Name: C-Novation-Pay Payments for WooCommerce
 * Plugin URI: https://www.c-novation-pay.com/
 * Description: Provides C-Novation-Pay as payment method to WooCommerce.
 * Author: Scavix Software Ltd. & Co. KG
 * Author URI: https://www.scavix.com/
 * Version: 0.0.1
 * Text Domain: c-novation-pay-for-woocommerce
 * Domain Path: /languages
 *
 * WC requires at least: 3.3.0
 * WC tested up to: 3.5.4
 *
 * Copyright (c) 2019 Scavix Software Ltd. & Co. KG
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


define( 'CP_GATEWAY_WC_VERSION', '0.0.1' );


function addGateway($methods)
{
	$methods[] = 'CNovationPayGateway';
	return $methods;
}

function init_cp_gateway_class() {

	include_once dirname( __FILE__ ) . '/includes/CNovationPayGateway.php';
	include_once dirname( __FILE__ ) . '/includes/CNovationPayClient.php';
	add_filter('woocommerce_payment_gateways', 'addGateway');

}

add_action( 'plugins_loaded', 'init_cp_gateway_class');



