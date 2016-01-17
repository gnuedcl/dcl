{extends file="_Layout.tpl"}
{block name=title}{$VAL_ATTRIBUTESETNAME|escape}{/block}
{block name=content}
<h2>{$smarty.const.STR_ATTR_ATTRIBUTESET|escape}: {$VAL_ATTRIBUTESETNAME|escape}</h2>
	{$TableHtml}
{/block}