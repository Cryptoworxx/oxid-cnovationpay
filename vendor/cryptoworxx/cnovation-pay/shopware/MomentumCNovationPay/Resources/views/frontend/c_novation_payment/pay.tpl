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
<style>
.field
{
    margin-bottom: 14px;
}
.field label
{ 
    display: block; 
    margin-bottom: 2px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.field &:not(.radio):not(.checkbox) label
{
    font-weight: 700;
}
.field span.labelled
{
    padding-left: 10px;
}
.field input:not([type="checkbox"]), .field textarea, .field select
{
    width: 100%;
    margin-top: 3px;
    font-size: 14px;
    color: black;
}
</style>
<div style="width: 500px; max-width: 100%; margin:auto;">
  
    <h1>{$text.TITLE_PAY}</h1>
    <p>{$text.TXT_PAY_INTRO}</p>
    
    <div class="field">
        <label>{$text.TXT_PAY_WALLET}</label>
        <input readonly="readonly" value="{$payment.wallet}" maxlength="255" class="ui-corner-all" type="text">
    </div>
    
    <div style="display:flex; justify-content: space-between;">
        <div class="field">
            <label>{$text.TXT_PAY_CURRENCY}</label>
            <input readonly="readonly" value="{$payment.currency.name}" maxlength="255" class="ui-corner-all" type="text"/>
        </div>
        <div class="field">
            <label>{$text.TXT_PAY_PRICE}</label>
            <input readonly="readonly" value="{$payment.price} {$payment.currency.code}" maxlength="255" class="ui-corner-all" type="text"/>
        </div>
    </div>
    <img style="border: 1px solid black; padding: 10px; margin: 0px 20px 0px 0px;" src="{$payment.urls.qrcode}">
    
    <div style="display:flex; justify-content: space-between;">
        <a href="{$cancelUrl}">{$text.TXT_PAY_ABORT}</a>
        <a href="{$finishUrl}">{$text.TXT_PAY_OK}</a>
    </div>
</div>
{/block}
