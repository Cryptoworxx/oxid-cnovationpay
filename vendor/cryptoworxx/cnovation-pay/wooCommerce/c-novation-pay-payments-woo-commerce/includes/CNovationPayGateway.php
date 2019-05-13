<?php


class CNovationPayGateway extends WC_Payment_Gateway
{

	/**
	 * Whether or not logging is enabled
	 *
	 * @var bool
	 */
	public static $log_enabled = true;

	/**
	 * Logger instance
	 *
	 * @var WC_Logger
	 */
	public static $log = false;

	/**
	 * Endpoint for requests from PayPal.
	 *
	 * @var string
	 */
	protected $notify_url;


	public function __construct()
	{
		$this->id = 'c_novation_pay_gw';
		$this->icon = ''; // If you want to show an image next to the gateway’s name on the frontend, enter a URL to an image.
		$this->has_fields = false; // Bool. Can be set to true if you want payment fields to show on the checkout (if doing a direct integration).
		$this->method_title = 'C-Novation Pay'; // Title of the payment method shown on the admin page.
		$this->method_description = 'Provides C-Novation Pay Crypto Payment';

		$this->description  = $this->get_option( 'description' );

		$this->order_button_text = __( 'proceed to C-Novation Pay', 'c-novation-pay-for-woocommerce' );


		$this->notify_url = WC()->api_request_url( 'WC_Gateway_CNovationPay' );


		$this->init_form_fields();
		$this->init_settings();

		$this->title = $this->get_option( 'title' );

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		add_action( 'woocommerce_api_wc_gateway_cnovationpay', array( $this, 'check_ipn_response' ) );

	}

	public function check_ipn_response(){
		/**
		 * handle the ipn response from c-novation-pay
		 */
		if(isset($_POST['ident']) && !empty($_POST['ident'])){
			$order_id = wc_get_order_id_by_order_key($_POST['ident']);
			if($order_id == $_POST['reference']){
				$order = wc_get_order($order_id);
				if($order->get_id()){
					switch ($_POST['status']){
						case 'pending':
							$order_status = 'pending';
							break;
						case 'cancelled':
							$order_status = 'cancelled';
							break;
						case 'failed':
							$order_status = 'failed';
							break;
						case 'finished':
							$order_status = 'processing';
							break;
						default:
							$order_status = 'pending';
					}
					if($order_status == 'processing'){
						$order->payment_complete();
						self::log('Finishing OrderStatus for Order ' . $order->get_id() . ': ' . $order_status);
					}else{
						self::log('Updating OrderStatus for Order ' . $order->get_id() . ': ' . $order_status);
						$order->update_status($order_status, 'Status Update from IPN Respnse');
					}
				}
			}
		}
	}


	public function process_payment($order_id) {
		$order = wc_get_order( $order_id );
		/**
		 * Get the Redirect Url, set Payment
		 */
		$url = $this->get_request_url_checkout($order);
		if($url !== false){
			return [
				'result'	=> 'success',
				'redirect'	=> $url
			];
		}else{
			$this->add_error('Fehler in get_request_url_checkout');
			return false;
		}
	}


	/**
	 * @param WC_Order $order
	 * @return string
	 */
	public function get_request_url_checkout($order) {

		self::log( 'Generating payment form for order ' . $order->get_order_number() . '. Notify URL: ' . $this->notify_url );

		$token = $this->get_option('cnovationpay_token');
		$api_endpoint = $this->get_option('cnovationpay_api_endpoint');
		$order_key = $order->get_order_key();

		$CPApiClient = new CNovationPayClient($api_endpoint, $token, false, $this->isSandbox());

		try{
			$res = $CPApiClient->checkout($order_key, $order->get_total(), $order->get_currency(), $order->get_order_number(), $this->notify_url, $this->get_return_url(), $order->get_cancel_order_url_raw());
			if($res !== false){
				$checkout_uid = $res['checkout_uid'];
				$checkout_url = $res['checkout_url'];
				$checkout_ident = $res['ident'];
				if($checkout_ident !== $order_key){
					/**
					 * something went wrong
					 */
					$this->add_error("Response Ident doesn't match");
					return false;
				}else{
					$order->set_transaction_id($checkout_uid);
					$order->save();
					return $checkout_url;
				}
			}else{
				$this->add_error($CPApiClient->error['message']);
				self::log('CNovationPayError Code: ' . $CPApiClient->error['code']);
				self::log('CNovationPayError Message: ' . $CPApiClient->error['message']);
				self::log('CNovationPayError Info: ' . $CPApiClient->error['info']);
				return false;
			}
		}catch (Exception $e){
			$this->add_error($e->getMessage());
			self::log($e->getMessage());
			return false;
		}
	}

	public function init_form_fields() {
		$api_endpoint = $this->get_option('cnovationpay_api_endpoint');
		$this->form_fields = array(
			'enabled' => array(
				'title' => __( 'Enable/Disable', 'c-novation-pay-for-woocommerce' ),
				'type' => 'checkbox',
				'label' => __( 'Enable C-Novation Pay Payment', 'c-novation-pay-for-woocommerce' ),
				'default' => 'yes'
			),
			'title' => array(
				'title' => __( 'Title', 'c-novation-pay-for-woocommerce' ),
				'type' => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'c-novation-pay-for-woocommerce' ),
				'default' => __( 'C-Novation Pay Crypto Payment', 'c-novation-pay-for-woocommerce' ),
				'desc_tip'      => true,
			),
			'description' => array(
				'title' => __( 'Customer Message', 'c-novation-pay-for-woocommerce' ),
				'type' => 'textarea',
				'default' => __( 'pay with ETH, BTC or other Cryptocurrencies', 'c-novation-pay-for-woocommerce' ),
			),
			'cnovationpay_api_endpoint'	=> array(
				'title'			=> __('C-Novation Pay API Endpoint', 'c-novation-pay-for-woocommerce'),
				'type'			=> 'text',
				'default' => 'https://www.c-novation-pay.com/api',
			),
			'cnovationpay_token'	=> array(
				'title'			=> __('C-Novation Pay API Token', 'c-novation-pay-for-woocommerce'),
				'type'			=> 'text',
				'description' => sprintf( __( 'This token is needed to connect to the C-Novation Pay service. <a href="%s" target="_blank">Get a Token now</a>.', 'c-novation-pay-for-woocommerce' ), $api_endpoint . '/authenticate?requesting_system=' . $_SERVER['HTTP_HOST'] ),
			)

		);
	}


	/**
	 * Logging method.
	 *
	 * @param string $message Log message.
	 * @param string $level Optional. Default 'info'. Possible values:
	 *                      emergency|alert|critical|error|warning|notice|info|debug.
	 */
	public static function log($message, $level = 'info')
	{
		if ( self::$log_enabled ) {
			if ( empty( self::$log ) ||  self::$log !== false) {
				self::$log = wc_get_logger();
			}
			if(is_object($message) || is_array($message)){
				ob_start();
				var_dump($message);
				$message = ob_get_contents();
				ob_end_clean();
			}


			self::$log->log( $level, $message, array( 'source' => 'cnovationpay' ) );
		}
	}

	public function isSandbox(){
		return false;
	}

}