<?php
namespace MomentumCNovationPay;

use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;

class MomentumCNovationPay extends \Shopware\Components\Plugin
{
    public static function Log()
    {
        $args = [date("Y-m-d H:i:s")];
        foreach( func_get_args() as $a )
            $args[] = "$a";
        error_log(implode("\t",$args)."\n", 3, Shopware()->DocPath() . '/var/log/cnovationpay-'.date('Y-m-d').'.log');
    }
    
    private function clearCache($context)
	{
        $context->scheduleClearCache([
			InstallContext::CACHE_TAG_CONFIG,
			InstallContext::CACHE_TAG_HTTP,
            InstallContext::CACHE_TAG_ROUTER,
		]);
	}
    
    private function setActiveFlag($context, $active)
    {
        $em = $this->container->get('models');
        $payments = $context->getPlugin()->getPayments();

        foreach ($payments as $payment) {
            $payment->setActive($active);
        }
        $em->flush();
    }
    
    private function readSecret($token)
    {
        $secret = '';
        if( !$token )
            return $secret;
        try
        {
            $filesystem = $this->container->get('shopware.filesystem.private');
            if( $filesystem->has('credentials.json') )
            {
                $data = $filesystem->read('credentials.json');
                $data = json_decode($data,true);
                if( $data && isset($data['token']) && isset($data['secret']) && $data['token']==$token )
                    $secret = $data['secret'];
            }
        }
        catch (Exception $ex){}
        return $secret;
    }
    
    private function writeSecret($token,$secret)
    {
        if( !$token || !$secret )
            return;
        $filesystem = $this->container->get('shopware.filesystem.private');
        if ($filesystem->has('credentials.json'))
            $filesystem->delete('credentials.json');
        $filesystem->write('credentials.json', json_encode(compact('token','secret')));
    }
    
    private $apiClient = false;
    public function getApiClient()
    {
        if( !$this->apiClient )
        {
            $token = Shopware()->Config()->get('cnovationpay_token','');
            $secret = $this->readSecret($token);
            
            require_once(__DIR__."/lib/CNovationPayClient.php");
            $this->apiClient = new \CNovationPayClient($token,$secret);
            $this->apiClient->url = Shopware()->Config()->get('cnovationpay_url','https://www.c-novation-pay.com/api');
            
            if( $token && !$secret )
            {
                $secret = $this->apiClient->register($_SERVER['SERVER_NAME'],'Shopware');
                if( $secret )
                    $this->writeSecret($token,$secret);
            }
        }
        return $this->apiClient;
    }
    
    public function getStrings()
    {
        $strings =
        [
            'de' =>
            [
                'TITLE_INDEX'      => 'Zahlung abschließen',
                'TXT_INDEX_INTRO'  => 'Bitte Wählen Sie die Cryptowährung aus, mit der Sie Ihre Bestellung bezahlen möchten.',
                'TXT_INDEX_ABORT'  => 'Abbrechen',
                'TXT_INDEX_OK'     => 'Weiter',
                'TITLE_PAY'        => 'Zahlung abschließen',
                'TXT_PAY_INTRO'    => 'Bitte leiten Sie die Zahlung ein. Sie können diese Seite dann schließen, der Zahlungsvorgang wird im Hintergrund verarbeitet.',
                'TXT_PAY_WALLET'   => 'Wallet',
                'TXT_PAY_CURRENCY' => 'Währung',
                'TXT_PAY_PRICE'    => 'Preis',
                'TXT_PAY_ABORT'    => 'Zahlung abbrechen',
                'TXT_PAY_OK'       => 'Fertig',
                'TXT_PLUGIN_DESCRIPTION' => 'C-Novation Pay - Kryptopayment',
            ],
            'en' =>
            [
                'TITLE_INDEX'      => 'Complete payment',
                'TXT_INDEX_INTRO'  => 'Choose a currency to pay the order.',
                'TXT_INDEX_ABORT'  => 'Cancel',
                'TXT_INDEX_OK'     => 'Continue',
                'TITLE_PAY'        => 'Complete payment',
                'TXT_PAY_INTRO'    => 'Pleas pay now. You may then close this page, the actual payment will be processed in the background.',
                'TXT_PAY_WALLET'   => 'Wallet',
                'TXT_PAY_CURRENCY' => 'Currency',
                'TXT_PAY_PRICE'    => 'Price',
                'TXT_PAY_ABORT'    => 'Cancel payment',
                'TXT_PAY_OK'       => 'Done',
                'TXT_PLUGIN_DESCRIPTION' => 'C-Novation Pay - Cryptopayment',
            ],
        ];
        
        $shop = $this->container->has('shop')?$this->container->get('shop'):false;
        if( $shop )
            $locale = $shop->getLocale()->getLocale();
        else
        {
            $locale = 'de_DE';
            try
            {
                $locale = $this->container->get('Auth')->getIdentity()->locale->getLocale();
            } catch (Exception $ex) {}
        }
        $lang = explode("_",$locale)[0];
        if( !isset($strings[$lang]) )
            $lang = 'de';
        
        if( $lang != 'de' )
        {
            // fill missing translations with defaults
            foreach( $strings['de'] as $k=>$v )
                if( !isset($strings[$lang][$k]) )
                    $strings[$lang][$k] = $v;
        }
        
        return $strings[$lang];
    }
    
    public function getString($name)
    {
        return $this->getStrings()[$name];
    }
    
    public function install(InstallContext $context)
	{
		$this->clearCache($context);
		parent::install($context);
        
        /** @var \Shopware\Components\Plugin\PaymentInstaller $installer */
        $installer = $this->container->get('shopware.plugin_payment_installer');

        $options = [
            'name' => 'cnovation_payment',
            'description' => $this->getString('TXT_PLUGIN_DESCRIPTION'),
            'action' => 'CNovationPayment',
            'active' => 0,
            'position' => 0,
            'additionalDescription' => ''
        ];
        $installer->createOrUpdate($this->getName(), $options);
 	}
 
	public function uninstall(UninstallContext $context)
	{
		$this->clearCache($context);
		parent::uninstall($context);
        $this->setActiveFlag($context, false);
	}
 
	public function activate(ActivateContext $context)
	{
		$this->clearCache($context);
		parent::activate($context);
        $this->setActiveFlag($context, true);
        $this->apiClient = false;
        $this->getApiClient();
	}
 
	public function deactivate(DeactivateContext $context)
	{
		$this->clearCache($context);
		parent::deactivate($context);
        $this->setActiveFlag($context, false);        
	}
    
}