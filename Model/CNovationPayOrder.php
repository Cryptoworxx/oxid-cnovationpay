<?php


namespace Momentum\CNovationPay\Model;


use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;

class CNovationPayOrder extends CNovationPayOrder_parent
{

    public function finalizeOrder(Basket $oBasket, $oUser, $blRecalculatingOrder = false)
    {

        $iRet = parent::finalizeOrder($oBasket, $oUser, $blRecalculatingOrder);
        if($iRet == 1 || $iRet == 0){
            $this->setPaymentInfoCNovationPay(Registry::getSession()->getVariable("cp-paymentid"));
        }
        return $iRet;
    }

    private function setPaymentInfoCNovationPay($paymentid)
    {
        // set transaction ID and payment date to order
        $db = DatabaseProvider::getDb();

        $query = 'update oxorder set oxtransid=' . $db->quote($paymentid) . ' where oxid=' . $db->quote($this->getId());
        $db->execute($query);

        //updating order object
        $this->oxorder__oxtransid = new Field($paymentid);
    }

}