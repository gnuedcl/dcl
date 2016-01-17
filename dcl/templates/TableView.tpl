{extends file="_Layout.tpl"}
{block name=title}{if $caption ne ""}{$caption|escape}{/if}{/block}
{block name=content}
{include file="Table.tpl"}
{/block}