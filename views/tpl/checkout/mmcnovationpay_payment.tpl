[{capture append="oxidBlock_content"}]
	[{if !$confError}]
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 id="paymentHeader" class="panel-title">[{oxmultilang ident="MMCNOVATIONPAY_PAYMENT_TITLE"}]</h3>
			</div>
			<div class="panel-body">
				<p>[{oxmultilang ident="MMCNOVATIONPAY_PAYMENT_INTRO"}]</p>
				<div class="row">
					<div class="col-lg-offset-2 col-lg-8">
						<dl>
							<dt><label>[{oxmultilang ident="MMCNOVATIONPAY_PAYMENT_WALLET"}]</label> [{$payment.wallet}]</dt>
							<dd></dd>
						</dl>
						<dl>
							<dt><label>[{oxmultilang ident="MMCNOVATIONPAY_PAYMENT_CURRENCY"}]</label> [{$payment.currency.name}]</dt>
							<dd></dd>
						</dl>
						<dl>
							<dt><label>[{oxmultilang ident="MMCNOVATIONPAY_PAYMENT_PRICE"}]</label> [{$payment.currency.code}]</dt>
							<dd></dd>
						</dl>
						<dl>
							<dt>
								<img style="border: 1px solid black; padding: 10px; margin: 0 20px 0 0;" src="[{$payment.urls.qrcode}]">
							</dt>
						</dl>
					</div>
				</div>

			</div>
		</div>
		<form action="[{$oViewConf->getSslSelfLink()}]" method="post" id="orderConfirmAgbBottom" class="form-horizontal">
			<div class="hidden">
				[{$oViewConf->getHiddenSid()}]
				[{$oViewConf->getNavFormParams()}]
				<input type="hidden" name="cl" value="order">
				<input type="hidden" name="fnc" value="execute">
				<input type="hidden" name="execute" value="1">
				<input type="hidden" name="challenge" value="[{$challenge}]">
				<input type="hidden" name="sDeliveryAddressMD5" value="[{$oView->getDeliveryAddressMD5()}]">
			</div>

			<div class="well well-sm cart-buttons">
				<a href="[{$cancelUrl}]" class="btn btn-default pull-left prevStep submitButton largeButton">[{oxmultilang ident="MMCNOVATIONPAY_INDEX_ABORT"}]</a>
				<button onclick="cnovationpay_goon()" class="btn btn-primary pull-right submitButton nextStep largeButton">[{oxmultilang ident="MMCNOVATIONPAY_PAYMENT_DONE"}]</button>
				<div class="clearfix"></div>
			</div>
		</form>

	[{/if}]
[{/capture}]

[{include file="layout/page.tpl"}]