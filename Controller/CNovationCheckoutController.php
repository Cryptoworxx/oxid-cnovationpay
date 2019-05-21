<?php


namespace Momentum\CNovationPay\Controller;


use Momentum\CNovationPay\Core\CNovationPayService;
use OxidEsales\Eshop\Application\Model\Address;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsView;

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

            $ApiClient = CNovationPayService::getApiClient();
            $currencies = $ApiClient->currencies();
            foreach ($currencies as &$cur) {
                $args['curcode'] = $cur['code'];
                $cur['url'] = Registry::getConfig()->getShopSecureHomeURL() . 'cl=CNovationCheckout&fnc=createPayment&' . http_build_query($args);
            }
            $this->addTplParam('currencies', $currencies);
            return $template;
        } else {
            Registry::getUtils()->redirect(
                Registry::getConfig()->getShopSecureHomeUrl() . "cl=basket", false
            );
        }
        $result = parent::render();

        return $result;
    }

    public function createPayment()
    {
        $request = Registry::getRequest();
        $curcode = $request->getRequestEscapedParameter('curcode');
        $ApiClient = CNovationPayService::getApiClient();
        $oSession = Registry::getSession();
        $oBasket = $oSession->getBasket();

        // reload blocker
        if ($oSession->getVariable('sess_challenge')) {
            $sGetChallenge = Registry::getUtilsObject()->generateUID();
            $oSession->setVariable('sess_challenge', $sGetChallenge);
        }


        $payment = $ApiClient->createPayment(
            $curcode,
            $oBasket->getPrice()->getBruttoPrice(),
            $oBasket->getBasketCurrency()->name,
            $oSession->getVariable('sess_challenge'),
            null,
            Registry::getConfig()->getShopSecureHomeURL() . 'cl=CNovationCheckout&fnc=callBack'
        );
        if ($payment) {

            return 'CNovationCheckout?fnc=pay&uid=' . $payment['uid'];
        }else{
            die('test');
        }
    }

    public function pay()
    {
        $ApiClient = CNovationPayService::getApiClient();
        $request = Registry::getRequest();
        $uid = $request->getRequestEscapedParameter('uid');
        $oSession = Registry::getSession();
        $oBasket = $oSession->getBasket();

        $payment = $ApiClient->payment($uid);
        if($payment !== false){
//            var_dump($payment);
            if($payment['requested_price'] != $oBasket->getPrice()->getBruttoPrice()){
                /**
                 * check whether the price has changed in the meantime
                 */

                $ApiClient->deletePayment($uid);
                Registry::get(UtilsView::class)->addErrorToDisplay(
                    Registry::getLang()->translateString('MMCNOCATIONPAY_PAYMENT_INVALID', null, true)
                );
                $oSession->deleteVariable('cp-paymentid');

                return 'basket';
            }

            $this->setTemplateName('mmcnovationpay_payment.tpl');
            $this->addTplParam('payment', $payment);
            $this->addTplParam('cancelUrl', Registry::getConfig()->getShopSecureHomeURL() . 'cl=CNovationCheckout&fnc=cancelPayment&uid=' . $payment['uid']);
            $oSession->setVariable('cp-paymentid', $uid);
        }else{
            Registry::get(UtilsView::class)->addErrorToDisplay(
                Registry::getLang()->translateString('MMCNOCATIONPAY_PAYMENT_NOT_FOUND', null, true)
            );

            return 'CNovationCheckout';
        }
    }

    public function cancelPayment()
    {
        $ApiClient = CNovationPayService::getApiClient();
        $uid = Registry::getRequest()->getRequestEscapedParameter('uid');
        $ApiClient->deletePayment($uid);
        return 'basket';
    }


    public function callBack()
    {

    }

    public function getDeliveryAddressMD5()
    {
        // bill address
        $oUser = $this->getUser();
        $sDelAddress = $oUser->getEncodedDeliveryAddress();

        // delivery address
        if (Registry::getSession()->getVariable('deladrid')) {
            $oDelAdress = oxNew(Address::class);
            $oDelAdress->load(Registry::getSession()->getVariable('deladrid'));

            $sDelAddress .= $oDelAdress->getEncodedDeliveryAddress();
        }

        return $sDelAddress;
    }





}