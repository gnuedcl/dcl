{extends file="_Layout.tpl"}
{block name=title}Outages{/block}
{block name=content}
<div id="outage-log">
	<h4>Outages {if $PERM_ADD}<small><a class="pull-right btn btn-success" href="{dcl_url_action controller=Outage action=Create}">{$smarty.const.STR_CMMN_NEW}</a></small>{/if}</h4>
	<ul id="pager" class="pager">
		<li class="previous"><a href="javascript:;">&larr; Newer</a></li>
		<li class="next"><a href="javascript:;">Older &rarr;</a></li>
	</ul>
	<table class="table table-striped">
		<thead>
		<tr><th>ID</th><th>Type</th><th>Title</th><th>Status</th><th>Start</th><th>End</th><th># Orgs</th><th># Environments</th><th># Work Orders</th></tr>
		</thead>
		<tbody id="error-list" data-bind="foreach: rows">
		<tr>
			<td><a href="javascript:;" data-bind="text: id, attr: { 'data-outage-id': id }"></a></td>
			<td data-bind="text: type"></td>
			<td data-bind="text: title"></td>
			<td data-bind="text: status"></td>
			<td data-bind="text: start"></td>
			<td data-bind="text: end"></td>
			<td data-bind="text: orgs"></td>
			<td data-bind="text: env"></td>
			<td data-bind="text: wo"></td>
		</tr>
		</tbody>
	</table>
</div>
{/block}
{block name=script}
<script type="text/javascript" src="{$DIR_VENDOR}knockout/knockout-3.3.0.js"></script>
<script type="text/javascript">
	$(function() {
		var outageViewModel = {
			records: ko.observable(""),
			page: ko.observable(0),
			total: ko.observable(0),
			rows: ko.observableArray([])
		};

		ko.applyBindings(outageViewModel, document.getElementById("outage-log"));

		var urlMainPhp = "{$URL_MAIN_PHP}";

		var firstId = 0;
		var lastId = 0;
		function updateOutage(dir) {
			$.ajax({
				type: "POST",
				url: urlMainPhp,
				data: { menuAction: "OutageService.GetData", rows: 25, lastid: lastId, firstid: firstId, dir: dir },
				dataType: "json"
			}).done(function(data) {
				outageViewModel.records(data.records);
				outageViewModel.page(data.page);
				outageViewModel.total(data.total);
				outageViewModel.rows(data.rows);

				if (data.rows.length > 0) {
					lastId = data.rows[data.rows.length - 1].id;
					firstId = data.rows[0].id;
				} else {
					lastId = 0;
					firstId = 0;
				}

				if (data.min > 0 && data.min >= lastId)
					$("#pager").find("li.next").addClass("disabled");
				else
					$("#pager").find("li.next").removeClass("disabled");

				if (data.max > 0 && firstId >= data.max)
					$("#pager").find("li.previous").addClass("disabled");
				else
					$("#pager").find("li.previous").removeClass("disabled");
			}).error(function() {
				$.gritter.add({ title: "Error", text: "Could not read outage log." });
				errorViewModel.refreshed("Error");
			});
		}

		updateOutage("next");

		var $pager = $("#pager");
		$pager.find("li.previous").click(function() {
			if (!$(this).hasClass("disabled"))
				updateOutage("previous");
		});

		$pager.find("li.next").click(function() {
			if (!$(this).hasClass("disabled"))
				updateOutage("next");
		});

		function htmlEncode(t) {
			return $("<div/>").text(t).html();
		}

		$("#outage-log").on("click", "a[data-outage-id]", function() {
			var id = $(this).attr("data-outage-id");
			location.href = "{dcl_url_action controller=Outage action=Edit params="id="}" + id;
		});
	});
</script>
{/block}