<?php

class CNovationPayClient
{
	var $url = "https://www.c-novation-pay.com/api";
	var $token = false;
	var $secret = false;
    var $response = false;
    var $success = false;
    var $result = false;
    var $error = false;
	
	public function __construct($token=false, $secret=false)
	{
		$this->token = $token;
		$this->secret = $secret;
	}
	
	protected function callApi($path,$arguments=array())
	{
        $this->response = $this->success = $this->result = $this->error = false;
        
		$url = "{$this->url}/{$this->token}/{$path}";
        
        $arguments = array_filter($arguments,function($a){ return !is_null($a); });
       
        if( $this->secret )
            $arguments['secret'] = $this->secret;
        
		if( count($arguments)>0 )
			$url .= "?".http_build_query($arguments);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		
		$this->response = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);
		
		if( !$this->response )
			throw new CNovationException("CURL: {$error}");
		
		$json = json_decode($this->response,true);
		if( !$json )
			throw new CNovationException("INVALID JSON: {$this->response}");
            
        $this->success = $json['success'];
        
        if( !$this->success )
        {
            if( !isset($json['code']) || !$json['code'] )
                $json['code'] = 'ERR_UNKNOWN';
            $this->error = ['code'=>$json['code']];
            if( isset($json['message']) )
                $this->error['message'] = $json['message'];
            if( isset($json['info']) )
                $this->error['info'] = $json['info'];
            return false;
        }
        $this->result = $json['result'];
		return $this->result;
	}
	
	public function info() { return $this->callApi(""); }
    
    public function register($identifier,$type='')
    {
        if( !$this->secret )
        {
            $res = $this->callApi("register",compact('identifier','type'));
            if( isset($res['secret']) )
                $this->secret = $res['secret'];
        }
        return $this->secret;
    }
	
	public function currencies() { return $this->callApi("currencies"); }
	public function currency($code) { return $this->callApi("currencies/{$code}"); }

	public function wallets() { return $this->callApi("wallets"); }
	public function wallet($uid) { return $this->callApi("wallets/{$uid}"); }
	
	public function payments() { return $this->callApi("payments"); }
	public function payment($uid) { return $this->callApi("payments/{$uid}"); }
	public function createPayment($currency, $price, $price_currency, $reference, $deadline=null, $callback=null) 
    {
        return $this->callApi(
            "payments/create",
            compact('currency','price','price_currency','reference','deadline','callback')
        );
    }
	public function cancelPayment($uid) { return $this->callApi("payments/cancel/{$uid}"); }
}

class CNovationException extends \Exception { }