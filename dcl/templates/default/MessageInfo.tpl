{if $MESSAGE->bIsFirst && count($MESSAGE->aBacktrace) > 0}
<script language="JavaScript">
{literal}
function toggleBacktrace(sUUID)
{
	var o = document.getElementById("div" + sUUID);
	if (o)
	{
		var oSpan = document.getElementById("spn" + sUUID);
		if (o.style.display != "")
		{
			o.style.display = "";
			oSpan.innerHTML = "&lt;&lt;";
		}
		else
		{
			o.style.display = "none";
			oSpan.innerHTML = "&gt;&gt;";
		}
	}
}
{/literal}
</script>
{/if}
<div class="dcl_message_info">
	<span>{$MESSAGE->sTitle|escape}{if count($MESSAGE->aBacktrace) > 0}&nbsp;&nbsp;[ <a href="#" onclick="toggleBacktrace('{$MESSAGE->sUUID}');">{$smarty.const.STR_CMMN_BACKTRACE}&nbsp;<span id="spn{$MESSAGE->sUUID}">&gt;&gt;</span></a> ]{/if}:&nbsp;</span>
	{$MESSAGE->sMessage|escape}
	{if count($MESSAGE->aBacktrace) > 0}
	<div id="div{$MESSAGE->sUUID}" style="display: none;">
	{section name=message loop=$MESSAGE->aBacktrace}
	{if !$smarty.section.message.first}
	<b>{$MESSAGE->aBacktrace[message].file} ({$MESSAGE->aBacktrace[message].line})</b>
	:&nbsp;{$MESSAGE->aBacktrace[message].class}{$MESSAGE->aBacktrace[message].type}{$MESSAGE->aBacktrace[message].function}<br>
	{/if}
	{/section}
	</div>
	{/if}
</div>