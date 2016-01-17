{extends file="_Layout.tpl"}
{block name=title}{$TXT_TITLE|escape}{/block}
{block name=content}
<h2>{$TXT_TITLE|escape}</h2>
<p><strong>{$TXT_DCL|escape}:</strong> {$VAL_DCLVERSION|escape}</p>
<h2>{$TXT_YOURVER|escape}</h2>
<p><strong>{$TXT_YOURIP|escape}:</strong> {$VAL_REMOTEADDR|escape}</p>
<p><strong>{$TXT_YOURBROWSER|escape}:</strong> {$VAL_HTTPUSERAGENT|escape}</p>
{/block}