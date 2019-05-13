<?php


namespace Momentum\CNovationPay\Controller;


use OxidEsales\Eshop\Core\Registry;

/**
 * Class OrderController
 *
 * @package Momentum\CNovationPay\Controller
 * @mixin \OxidEsales\Eshop\Application\Controller\OrderController
 */

class OrderController extends OrderController_parent
{

	/**
	 * Checks if payment action is processed by C-Novation Pay
	 *
	 * @return bool
	 */
	public function isCNovationPay()
	{
		return (Registry::getSession()->getBasket()->getPaymentId() == "c_novation_pay");
//		return ($this->getSession()->getVariable("paymentid") == "c_novation_pay");

	}

	public function execute()
	{

		if($this->isCNovationPay()){
			return 'CNovationCheckout';
		}else{
			$result = parent::execute();
			return $result;
		}
	}

}