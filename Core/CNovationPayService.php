<?php


namespace Momentum\CNovationPay\Core;


class CNovationPayService
{

    /**
     * @var \CNovationPayClient||null
     */
    protected static $ApiClient = null;

    public static function getApiClient(){
        if(is_null(self::$ApiClient)){
            self::$ApiClient = new \CNovationPayClient(CNovationPayUtils::getShopConfVar('sCPApiToken'));
            self::$ApiClient->url = CNovationPayUtils::getShopConfVar('sCPApiEndpoint');
        }
        return self::$ApiClient;
    }





}