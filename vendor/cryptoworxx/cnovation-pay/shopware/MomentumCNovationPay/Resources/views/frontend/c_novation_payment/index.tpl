{extends file="frontend/index/index.tpl"}

{* Hide sidebar left *}
{block name='frontend_index_content_left'}
    {if !$theme.checkoutHeader}
        {$smarty.block.parent}
    {/if}
{/block}

{* Hide breadcrumb *}
{block name='frontend_index_breadcrumb'}{/block}

{block name="frontend_index_content"}
{block name='cnovationpay'}
<div style="width: 500px; max-width: 100%; margin:auto;">
    <script>
        function cnovationpay_goon()
        {
            var url = document.querySelector('[name="cur"]:checked').value;
            location.href = url;
        }
    </script>
    <h1>{$text.TITLE_INDEX}</h1>
    <p>{$text.TXT_INDEX_INTRO}</p>
    <div style="display:flex; flex-direction:column;">
        
        {foreach item=currency from=$currencies}
        <div style="margin-bottom: 15px">
            <input id="cur{$currency.code}" type="radio" name="cur" value="{$currency.url}"/>
            <label for="cur{$currency.code}">{$currency.name} ({$currency.code})</label>
        </div>
        {/foreach}
    
        <div style="text-align: right">
            <a href="{$cancelUrl}" style="margin-right: 25px">{$text.TXT_INDEX_ABORT}</a>
            <button onclick="cnovationpay_goon()">{$text.TXT_INDEX_OK}</button>
        </div>
    </div>
</div>
{/block}
{/block}
