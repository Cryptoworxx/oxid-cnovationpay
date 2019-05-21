<?php

namespace Momentum\CNovationPay\Core;

use OxidEsales\Eshop\Core\Registry;

class CNovationPayUtils
{
	/**
	 * @param $name
	 * @return mixed
	 */
	public static function getShopConfVar($name)
	{
		$config = Registry::getConfig();
		$shopId = $config->getShopId();
		return $config->getShopConfVar($name, $shopId, 'module:momentumc-novationpay');
	}

}