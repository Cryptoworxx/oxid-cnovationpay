<?php


namespace Momentum\CNovationPay\Controller;


use Momentum\CNovationPay\Core\CNovationPayUtils;
use OxidEsales\Eshop\Core\Registry;

class CNovationCheckoutController extends FrontendController
{
	/**
	 * @var string
	 */
	protected $_sThisTemplate = 'mmcnovationpay_checkout.tpl';


	public function init()
	{
		parent::init();
	}

	public function render()
	{
		if (Registry::getSession()->getVariable('paymentid') === "c_novation_pay") {
			$template = parent::render();

			$this->addTplParam("test", CNovationPayUtils::getShopConfVar('sCPApiEndpoint'));
			$test = new \CNovationPayClient(CNovationPayUtils::getShopConfVar('sCPApiEndpoint'));
			var_dump($test);

			return $template;
		}else{
			Registry::getUtils()->redirect(
				Registry::getConfig()->getShopSecureHomeUrl() . "cl=basket", false
			);
		}
		$result = parent::render();

		return $result;
	}


}