<?php

namespace Momentum\CNovationPay\Core;

class Events
{
	public static function onActivate()
	{
//		echo('test richtig');

//		$logger = \OxidEsales\Eshop\Core\Registry::getLogger();
//		$logger->alert('onActivate ...', [__CLASS__, __FUNCTION__]);
//
		// adding record to oxPayment table
		self::addPaymentMethod();
//
//		// enabling C-Novation Pay payment method
		self::enablePaymentMethod();
	}

	/**
	 * Add C-Novation Pay payment method set EN and DE long descriptions
	 */
	public function addPaymentMethod()
	{
		$paymentDescriptions = array(
			'en' => '<div>When selecting this payment method you are being redirected to C-Novation Pay.</div>',
			'de' => '<div>Bei Auswahl der Zahlungsart C-Novation Pay werden Sie im nächsten Schritt zu C-Novation Pay weitergeleitet.</div>'
		);
		$payment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);

		$payment->setId('c_novation_pay');
		$payment->oxpayments__oxactive = new \OxidEsales\Eshop\Core\Field(1);
		$payment->oxpayments__oxdesc = new \OxidEsales\Eshop\Core\Field('C-Novation Pay');
		$payment->oxpayments__oxaddsum = new \OxidEsales\Eshop\Core\Field(0);
		$payment->oxpayments__oxaddsumtype = new \OxidEsales\Eshop\Core\Field('abs');
		$payment->oxpayments__oxfromboni = new \OxidEsales\Eshop\Core\Field(0);
		$payment->oxpayments__oxfromamount = new \OxidEsales\Eshop\Core\Field(0);
		$payment->oxpayments__oxtoamount = new \OxidEsales\Eshop\Core\Field(10000);

		$language = \OxidEsales\Eshop\Core\Registry::getLang();
		$languages = $language->getLanguageIds();
		foreach ($paymentDescriptions as $languageAbbreviation => $description) {
			$languageId = array_search($languageAbbreviation, $languages);
			if ($languageId !== false) {
				$payment->setLanguage($languageId);
				$payment->oxpayments__oxlongdesc = new \OxidEsales\Eshop\Core\Field($description);
				$payment->save();
			}
		}
	}

	/**
	 * Activates C-Novation Pay payment method
	 */
	public static function enablePaymentMethod()
	{
		$payment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
		$payment->load('c_novation_pay');
		$payment->oxpayments__oxactive = new \OxidEsales\Eshop\Core\Field(1);
		$payment->save();
	}

	/**
	 * Disables C-Novation Pay payment method
	 */
	public static function disablePaymentMethod()
	{
		$payment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
		if ($payment->load('c_novation_pay')) {
			$payment->oxpayments__oxactive = new \OxidEsales\Eshop\Core\Field(0);
			$payment->save();
		}
	}


	/**
	 * Execute action on deactivate event
	 *
	 * @return null
	 */
	public static function onDeactivate()
	{
		$logger = \OxidEsales\Eshop\Core\Registry::getLogger();
		$logger->debug('onDeactivate ...', [__CLASS__, __FUNCTION__]);
		self::disablePaymentMethod();
	}

}