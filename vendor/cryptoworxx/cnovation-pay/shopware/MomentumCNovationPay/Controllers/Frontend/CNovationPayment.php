<?php

use MomentumCNovationPay\MomentumCNovationPay;
use Shopware\Components\CSRFWhitelistAware;

class Shopware_Controllers_Frontend_CNovationPayment extends Shopware_Controllers_Frontend_Payment implements CSRFWhitelistAware
{   
    public function View()
    {
        $res = parent::View();
        $res->assign('text',$this->get('kernel')->getPlugins()['MomentumCNovationPay']->getStrings());
        return $res;
    }
    
    public function getWhitelistedCSRFActions()
    {
        return [
            'callback'
        ];
    }
    
    public function preDispatch()
    {
        /** @var \Shopware\Components\Plugin $plugin */
        $plugin = $this->get('kernel')->getPlugins()['MomentumCNovationPay'];
        $this->get('template')->addTemplateDir($plugin->getPath() . '/Resources/views/');
    }

    public function indexAction()
    {
        if( $this->getPaymentShortName() != 'cnovation_payment' )
            return $this->redirect(['controller' => 'checkout']);
        
        $router = $this->Front()->Router();
        $args = [
            'transactionId' => random_int(0, 1000)
        ];
        
        $api = $this->get('kernel')->getPlugins()['MomentumCNovationPay']->getApiClient();
        $currencies = $api->currencies();
        foreach( $currencies as &$cur )
        {
            $args['curcode'] = $cur['code'];
            $cur['url'] = $router->assemble(['action' => 'createPayment', 'forceSecure' => true])
                .'?'.http_build_query($args);
        }
        unset($args['currency']);
        $this->View()->assign([
            'firstName' => $this->Request()->getParam('firstName'),
            'lastName' => $this->Request()->getParam('lastName'),
            'amount' => $this->Request()->getParam('amount'),
            'currency' => $this->Request()->getParam('currency'),
            'cancelUrl' => $router->assemble(['action' => 'cancel', 'forceSecure' => true])
                .'?'.http_build_query([$args]),
            'currencies' => $currencies
        ]);
    }
    
    public function createPaymentAction()
    {
        $router = $this->Front()->Router();
        $transactionId =  $this->createPaymentUniqueId();
        $uniqueId = $this->createPaymentUniqueId();
        
        $order_num = $this->saveOrder(
            $transactionId,
            $uniqueId,
            \Shopware\Models\Order\Status::PAYMENT_STATE_COMPLETELY_INVOICED
        );
        $orderId = $this->getOrderId($transactionId,$uniqueId);
        if( !$orderId )
        {
            $issue = md5(time());
            MomentumCNovationPay::Log("Issue $issue","Cannot get orderId (num is '$order_num')");
            return $this->gotoError("Fatal error occured. Please contact technical support using issue number $issue");
        }

        $api = $this->get('kernel')->getPlugins()['MomentumCNovationPay']->getApiClient();
        $payment = $api->createPayment(
            $this->Request()->getParam('curcode'),
            $this->getAmount(),
            $this->getCurrencyShortName(),
            $order_num,
            null,
            $router->assemble(['action' => 'callback']).'?'.http_build_query(compact('transactionId','uniqueId'))
        );
        if( !$payment )
        {
            $this->cancelOrder($orderId);
            $issue = md5(time());
            MomentumCNovationPay::Log("Issue $issue","Cannot create C-Novation payment for order '$order_num'",$api->error);
            return $this->gotoError("Fatal error occured. Please contact technical support using issue number $issue");
        }
        
        $uid = $payment['uid'];
        
        $url = $router->assemble(['action' => 'pay', 'forceSecure' => true])
            .'?'.http_build_query(compact('transactionId','uniqueId','uid'));
        $this->redirect($url);
    }

    public function payAction()
    {
        $router = $this->Front()->Router();
        $transactionId = $this->Request()->getParam('transactionId');
        $uniqueId = $this->Request()->getParam('uniqueId');
        
        $orderId = $this->getOrderId($transactionId,$uniqueId);
        if( !$orderId )
            return $this->redirect(['controller' => 'checkout', 'action' => 'finish']);
        
        $api = $this->get('kernel')->getPlugins()['MomentumCNovationPay']->getApiClient();
        $payment = $api->payment($this->Request()->getParam('uid'));
        if( !$payment )
        {
            $this->cancelOrder($orderId);
            $issue = md5(time());
            MomentumCNovationPay::Log("Issue $issue","C-Novation payment not found for orderId '$orderId'",$api->error);
            return $this->gotoError("Fatal error occured. Please contact technical support using issue number $issue");
        }
        
        $uid = $payment['uid'];
        $this->View()->assign([
            'payment' => $payment,
            'cancelUrl' => $router->assemble(['action' => 'cancel', 'forceSecure' => true])
                .'?'.http_build_query(compact('transactionId','uniqueId','uid')),
            'finishUrl' => $router->assemble(['controller' => 'checkout', 'action' => 'finish']),
        ]);
    }
    
    public function errorAction()
    {
        $message = $this->Request()->getParam('message');
        $this->View()->assign([
            'message' => $message
        ]);
    }

    public function cancelAction()
    {
        $transactionId = $this->Request()->getParam('transactionId');
        $uniqueId = $this->Request()->getParam('uniqueId');
        
        $orderId = $this->getOrderId($transactionId,$uniqueId);
        if( $orderId )
            $this->cancelOrder($orderId);
        
        $uid = $this->Request()->getParam('uid');
        if( $uid )
        {
            $api = $this->get('kernel')->getPlugins()['MomentumCNovationPay']->getApiClient();
            $api->cancelPayment($uid,"Cancelled by user in Shopware interface");
        }
        
        $this->redirect(['controller' => 'checkout']);
    }
    
    public function callbackAction()
    {
        $transactionId = $this->Request()->getParam('transactionId');
        $uniqueId = $this->Request()->getParam('uniqueId');
        $orderId = $this->getOrderId($transactionId,$uniqueId);
        if( !$orderId )
            die("notfound");
        
//        $uid = $this->Request()->getParam('uid');
//        $price = $this->Request()->getParam('price');
//        $reference = $this->Request()->getParam('reference');
        $status = $this->Request()->getParam('status');
        
        switch( $status )
        {
            case 'finished':
                $this->finishOrder($orderId);
                die("ok");
            case 'cancelled':
            case 'failed':
                $this->cancelOrder($orderId);
                MomentumCNovationPay::Log(__METHOD__,"C-Novation payment cancelled for orderId '$orderId'","Status: $status");
                die("ok");
        }
        die("unhandled");
    }
    
    private function gotoError($message)
    {
        $router = $this->Front()->Router();
        $url = $router->assemble(['action' => 'error']).'?'.http_build_query(compact('message'));
        return $this->redirect($url);
    }
    
    private function finishOrder($orderId)
    {
        $order = Shopware()->Modules()->Order();
        $order->setPaymentStatus($orderId, \Shopware\Models\Order\Status::PAYMENT_STATE_COMPLETELY_PAID);
//        $order->setOrderStatus($orderId, \Shopware\Models\Order\Status::ORDER_STATE_COMPLETED);
    }
    
    private function cancelOrder($orderId)
    {
        $order = Shopware()->Modules()->Order();
        $order->setPaymentStatus($orderId, \Shopware\Models\Order\Status::PAYMENT_STATE_THE_PROCESS_HAS_BEEN_CANCELLED);
//        $order->setOrderStatus($orderId, \Shopware\Models\Order\Status::ORDER_STATE_CANCELLED);
    }
    
    private function getOrderId($transactionId,$uniqueId)
    {
        $sql = 'SELECT id FROM s_order WHERE transactionID=? AND temporaryID=? AND status!=-1';
        return Shopware()->Db()->fetchOne($sql, [$transactionId,$uniqueId]);
    }
}
