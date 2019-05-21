[{capture append="oxidBlock_content"}]
	[{if !$confError}]
		<script>
			function cnovationpay_goon()
			{
				let sel = document.querySelector('[name="cur"]:checked');
				if(sel != null){
					location.href = document.querySelector('[name="cur"]:checked').value;
				}
			}
		</script>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 id="paymentHeader" class="panel-title">[{oxmultilang ident="MMCNOVATIONPAY_INDEX_TITLE"}]</h3>
			</div>
			<div class="panel-body">
				<p>[{oxmultilang ident="MMCNOVATIONPAY_INDEX_INTRO"}]</p>
				[{foreach item=currency from=$currencies}]
					<div class="well well-sm">
						<dl>
							<dt>
								<input id="cur[{$currency.code}]" type="radio" name="cur" value="[{$currency.url}]"/>
								<label for="cur[{$currency.code}]"><b>[{$currency.name}] ([{$currency.code}])</b></label>
							</dt>
						</dl>
					</div>
				[{/foreach}]
			</div>
		</div>

	<div class="well well-sm cart-buttons">
		<a href="[{$cancelUrl}]" class="btn btn-default pull-left prevStep submitButton largeButton">[{oxmultilang ident="MMCNOVATIONPAY_INDEX_ABORT"}]</a>
		<button onclick="cnovationpay_goon()" class="btn btn-primary pull-right submitButton nextStep largeButton">[{oxmultilang ident="MMCNOVATIONPAY_INDEX_OK"}]</button>
		<div class="clearfix"></div>
	</div>
	[{/if}]
[{/capture}]

[{include file="layout/page.tpl"}]