<table class="wikiPage" cellspacing="0" cellpadding="2">
	<tr><th class="header"><a class="wiki" href="{$LNK_ACTION}?menuAction=htmlWiki.show&name={$VAL_PAGENAME|escape:url}&type={$VAL_ENTITYTYPEID}&id={$VAL_ENTITYID}&id2={$VAL_ENTITYID2}">{$VAL_TITLE|escape}</a></th></tr>
	{if $VAL_DESCRIPTION}<tr><td>{$VAL_DESCRIPTION|escape}&nbsp;&nbsp;<a class="wiki" href="{$LNK_DESCRIPTION}">{$TXT_VIEW|escape}</a></td></tr>{/if}
	<tr><td style="background-color: white;">
{if $VAL_MODE != "edit"}{$VAL_TEXT}{/if}
{if $VAL_MODE == "edit"}
		<form method="post" action="{$LNK_ACTION}">
		<input type="hidden" name="menuAction" value="htmlWiki.postwiki">
		<input type="hidden" name="editmode" value="done">
		<input type="hidden" name="name" value="{$VAL_PAGENAME|escape}">
		<input type="hidden" name="type" value="{$VAL_ENTITYTYPEID}">
		<input type="hidden" name="id" value="{$VAL_ENTITYID}">
		<input type="hidden" name="id2" value="{$VAL_ENTITYID2}">
		<textarea rows="18" cols="70" name="text" style="width:100%" wrap="virtual">{$VAL_TEXT}</textarea><br>
		<input class="wiki" type="submit" value="{$TXT_SAVE}">&nbsp;
		<input class="wiki" name="r" type="reset" value="{$TXT_RESET}">&nbsp;
		<input class="wiki" type="button" value="{$TXT_CANCEL}" onclick="location.href='{$LNK_CANCEL}';">
		</form>
	</td></tr>
	<tr>
	<td>
		<hr noshade size="1">
<b>Emphasis:</b> ''<i>italics</i>''; '''<b>bold</b>'''; '''''<b><i>bold italics</i></b>'''''; ''<i>mixed '''<b>bold</b>''' and italics</i>''; ---- horizontal rule.<br>
<b>Headings:</b> = Title 1 =; == Title 2 ==; === Title 3 ===; ==== Title 4 ====; ===== Title 5 =====.<br>
<b>Lists:</b> * bullets; 1., a., A., i., I. numbered items; 1.#n start numbering at n<!--; space alone indents-->.<br>
<b>Links:</b> JoinCapitalizedWords; ((WikiPageName|Link Text)); url.<br>
<b>Image:</b> [url]
<b>Tables:</b> || cell text |||| cell text spanning two columns ||; no trailing white space allowed after tables or titles.<br>
{/if}
	</td></tr>
	<tr><td class="footer">
{if $VAL_MODE == "view"}<a class="wiki" href="{$LNK_EDITTHISPAGE}">{$TXT_EDITTHISPAGE|escape}</a>&nbsp;|&nbsp;{/if}
{if $VAL_MODE == "locked"}{$TXT_THISPAGECANNOTBEEDITED|escape}&nbsp;|&nbsp;{/if}
		<a class="wiki" href="{$LNK_RETURNTOFRONTPAGE}">{$TXT_RETURNTOFRONTPAGE|escape}</a>&nbsp;|&nbsp;
		<a class="wiki" href="{$LNK_RECENTCHANGES}">{$TXT_VIEWRECENTCHANGES|escape}</a>
	</td></tr>
</table>
