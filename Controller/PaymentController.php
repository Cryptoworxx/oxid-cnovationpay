<?php

namespace Momentum\CNovationPay\Controller;

class PaymentController extends PaymentController_parent
{
	/**
	 * Detects is current payment must be processed by PayPal and instead of standard validation
	 * redirects to standard PayPal dispatcher
	 *
	 * @return bool
	 */
	public function validatePayment()
	{

		$paymentId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('paymentid');
//		if ($paymentId === 'c_novation_pay') {
//			return 'mmcnovationpaystandarddispatcher?fnc=setExpressCheckout';
//		}

		return parent::validatePayment();
	}
}