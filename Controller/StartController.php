<?php

namespace Momentum\CNovationPay\Controller;

class StartController extends StartController_parent
{

	public function render()
	{

		$logger = \OxidEsales\Eshop\Core\Registry::getLogger();
		$logger->alert('test ...', [__CLASS__, __FUNCTION__]);

		die('testdd');
	}

}