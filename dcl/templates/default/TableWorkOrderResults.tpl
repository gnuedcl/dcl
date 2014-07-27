{assign var=groupcount value=$groups|@count}
{assign var=colcount value=$columns|@count}
{if $rownum}{assign var=colcount value=$colcount+1}{/if}
{if $checks}{assign var=colcount value=$colcount+1}{/if}
<form name="searchAction" id="searchAction" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" id="menuAction" value="" />
	<input type="hidden" name="product" value="{$HID_PRODUCT}" />
	{$VAL_VIEWSETTINGS}
	{if $caption ne ""}<h4>{$caption|escape}</h4>{/if}
<table id="results" class="table table-striped{if $inline} inline{/if}"{if $width > 0} style="width:{$width};"{/if}>
{strip}
{section loop=$columns name=col}
{if $columns[col].title == $smarty.const.STR_WO_ID}{assign var=wo_id value=$smarty.section.col.index}{/if}
{if $columns[col].title == $smarty.const.STR_WO_SEQ}{assign var=seq value=$smarty.section.col.index}{/if}
	{if $smarty.section.col.first}<thead>
	{if $toolbar}
	<tr><td colspan="{$colcount}">
	{section loop=$toolbar name=tb}
	{if $smarty.section.tb.first}<div class="btn-group">{/if}
	<a class="btn btn-default" href="#" onclick="document.forms.searchAction.elements.menuAction.value='{$toolbar[tb].link}'; submitBatch();">{$toolbar[tb].text|escape}</a>
	{if $smarty.section.tb.last}</div>{/if}
	{/section}
	</td></tr>
	{/if}
	<tr>{if $checks}<th>{if $groupcount == 0}<input type="checkbox" name="group_check" onclick="javascript: toggle(this);">{/if}</th>{/if}{if $rownum}<th></th>{/if}{/if}{if !in_array($smarty.section.col.index, $groups)}<th>{$columns[col].title|escape}</th>{/if}{if $smarty.section.col.last}</tr></thead>{/if}
{/section}
{/strip}
{section loop=$footer name=item}
{if $smarty.section.item.first}<tfoot><tr>{if $checks}<td></td>{/if}{if $rownum}<td></td>{/if}{/if}{if !in_array($smarty.section.item.index, $groups)}<td class="{$columns[$smarty.section.item.index].type}">{$footer[item]|escape}</td>{/if}{if $smarty.section.item.last}</tr></tfoot>{/if}
{/section}
{section loop=$records name=row}
	{if $smarty.section.row.first}{strip}
		<tbody>
		{section loop=$groups name=group}
			{assign var=groupcol value=$groups[group]}
			{if $smarty.section.group.first}<tr class="group"><td colspan="{$colcount}">
				{if $checks}<input type="checkbox" name="group_check" onclick="javascript: toggle(this);">{/if}
			{/if}
			{$columns[$groupcol].title|escape}&nbsp;[&nbsp;{$records[row][$groupcol]|escape}&nbsp;]&nbsp;
			{if $smarty.section.group.last}</td></tr>{/if}
		{/section}
	{/strip}{elseif count($groups) > 0}{strip}
		{assign var=newgroup value=false}
		{foreach from=$groups item=value key=key}
			{if $records[row][$value] != $records[row.index_prev][$value]}
				{assign var=newgroup value=true}
			{/if}
		{/foreach}
		{if $newgroup == "true"}
			</tbody><tbody>
			{section loop=$groups name=group}
				{assign var=groupcol value=$groups[group]}
				{if $smarty.section.group.first}<tr class="group"><td colspan="{$colcount}">
					{if $checks}<input type="checkbox" name="group_check" onclick="javascript: toggle(this);">{/if}
				{/if}
				{$columns[$groupcol].title|escape}&nbsp;[&nbsp;{$records[row][$groupcol]|escape}&nbsp;]&nbsp;
				{if $smarty.section.group.last}</td></tr>{/if}
			{/section}
		{/if}{/strip}
	{/if}
	<tr>
	{if $checks}<td class="rowcheck"><input type="checkbox" name="selected[]" value="{$records[row][$wo_id_ordinal]}.{$records[row][$seq_ordinal]}"></td>{/if}
	{if $rownum}<td class="rownum">{$smarty.section.row.iteration}</td>{/if}
	{strip}
	{section loop=$records[row] name=item}
		{if !in_array($smarty.section.item.index, $groups) && $smarty.section.item.index < (count($records[row]) + $VAL_ENDOFFSET)}
			<td class="{$columns[$smarty.section.item.index].type}">
			{if $smarty.section.item.index == $wo_id_ordinal || $smarty.section.item.index == $seq_ordinal}<a href="{$URL_MAIN_PHP}?menuAction=WorkOrder.Detail&jcn={$records[row][$wo_id_ordinal]}&seq={$records[row][$seq_ordinal]}">{$records[row][item]}</a>
			{elseif $smarty.section.item.index == $tag_ordinal && $records[row][$num_tags_ordinal] > 1}{dcl_get_entity_tags entity=$smarty.const.DCL_ENTITY_WORKORDER key_id=$records[row][$wo_id_ordinal] key_id2=$records[row][$seq_ordinal] link=Y}
			{elseif $smarty.section.item.index == $tag_ordinal && $records[row][$num_tags_ordinal] == 1}{dcl_tag_link value=$records[row][item]}
			{elseif $smarty.section.item.index == $hotlist_ordinal && $records[row][$num_hotlist_ordinal] > 0}{dcl_get_entity_hotlist entity=$smarty.const.DCL_ENTITY_WORKORDER key_id=$records[row][$wo_id_ordinal] key_id2=$records[row][$seq_ordinal] link=Y}
			{elseif $smarty.section.item.index == $hotlist_ordinal && $records[row][$num_hotlist_ordinal] == 1}{dcl_hotlist_link value=$records[row][item]}
			{elseif $columns[$smarty.section.item.index].type == "html"}{$records[row][item]}
			{else}{$records[row][item]|escape}{if $records[row][$num_accounts_ordinal] > 1 && $smarty.section.item.index == $org_ordinal} <a href="javascript:;" data-woid="{$records[row][$wo_id_ordinal]}" data-seq="{$records[row][$seq_ordinal]}" class="view-orgs"><span class="badge">{$records[row][$num_accounts_ordinal]}</span></a>{/if}
			{/if}
			</td>
		{/if}
	{/section}
	{/strip}
	</tr>
	{if $smarty.section.row.last}</tbody>{/if}
{/section}
</table>
</form>
{dcl_modal}
<script type="text/javascript">

	function toggle(btnSender)
	{
		var bChk = btnSender.checked;
		var bOK = false;
		var e=btnSender.form.elements;
		for (var i=0;i<e.length;i++)
		{
			if (!bOK && e[i] == btnSender)
				bOK = true;
			else if (bOK && (e[i].type != "checkbox" || e[i].name == "group_check"))
				return;
			else if (bOK && e[i].type == "checkbox")
				e[i].checked = bChk;
		}
	}

	function submitBatch()
	{
		var f = document.forms.searchAction;
		var sAction = f.elements.menuAction.value;

		if (sAction == 'WorkOrder.BatchDetail' || sAction == 'boTimecards.batchadd' || sAction == 'WorkOrder.BatchReassign' || sAction == 'Project.Move' || sAction == 'Project.BatchMove')
		{
			var bHasCheck = false;
			for (var i = 0; i < f.elements.length && !bHasCheck; i++)
			{
				bHasCheck = (f.elements[i].type == "checkbox" && f.elements[i].name != "group_check" && f.elements[i].checked);
			}

			if (!bHasCheck)
			{
				alert('You must select one or more items!');
				return;
			}
		}
		f.submit();
	}

	$(document).ready(function() {
		function htmlEncode(t) {
			return $("<div/>").text(t).html();
		}

		$("a.view-orgs").click(function() {
			var woId = $(this).attr('data-woid');
			var seq = $(this).attr('data-seq');
			var $dialog = $("#dialog");
			$dialog.find("h4.modal-title").text("Accounts for Work Order " + woId + "-" + seq);
			$.ajax({
				url: "{$URL_MAIN_PHP}?menuAction=WorkOrderService.ListOrgs&wo_id=" + woId + "&seq=" + seq,
				dataType: "json",
				type: "GET",
				success: function(data) {
					if (data.count > 0) {
						var content = '<ul class="list-group">';
						for (var idx in data.rows) {
							content += '<li class="list-group-item">' + htmlEncode(data.rows[idx].name) + "</li>";
						}

						content += "</ul>";

						$dialog.find("div.modal-body > p").html(content);
					}
				}
			});

			$dialog.modal();
		});

		/*$("#menuAction").val("WorkOrderService.GetData");
		 $("#results").jqAjaxTable({
		 url: "{$URL_MAIN_PHP}",
		 form: "#searchAction"
		 });*/
	});
	(function($) {
		$.fn.jqAjaxTable = function(options) {
			var defaults = {
				url: "",
				rows: 25,
				columns: [],
				form: ""
			};

			var settings = $.extend(defaults, options);

			var methods = {
				id: "",
				pagerId: "",
				table: $([]),
				response: { count: 0, records: [], total: 0 },
				init: function(id) {
					this.id = id;
					this.pagerId = id + "Pager";
					this.table = $("#" + id);
					this.clearTable();
				},
				setPage: function(page) {
					this.getData(page);
				},
				getData: function(page) {
					$("#" + this.pagerId).text("Loading...");
					$.ajax({
						type: "POST",
						url: settings.url,
						data: $(settings.form).serialize() + "&page=" + page + "&rows=" + settings.rows,
						success: function(data) {
							$("#" + methods.pagerId).remove();
							if (data.count > 0) {
								var $div = $("<div/>");
								var html = "";
								$.each(data.records, function(ridx, row) {
									html += "<tr>";
									html += "<td><input type=\"checkbox\" /></td>";
									$.each(row, function(fidx, field) {
										if (field != null && field != "") {
											html += "<td>" + $div.text(field).html() + "</td>";
										}
										else {
											html += "<td>&nbsp;</td>";
										}
									});
									html += "</tr>";
								});

								methods.table.find("tbody").append(html);

								if (data.total > (page * settings.rows)) {
									$("<button class=\"jq-ajax-table-pager\"></button>").attr("id", methods.pagerId).text((data.total - (page * settings.rows)) + " More").appendTo(methods.table.parent()).click(function() {
										methods.getData(page + 1);
										return false;
									});
								}
							}
						}
					});
				},
				clearTable: function() {
					this.table.find("tbody").empty();
				}
			};

			return this.each(function() {
				var $this = $(this);
				methods.init($this.attr("id"));
				methods.setPage(1);
			});
		};
	})(jQuery);

</script>
