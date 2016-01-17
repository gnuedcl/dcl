{extends file="_Layout.tpl"}
{block name=title}[{$VAL_HOTLISTID}] {$VAL_NAME|escape}{/block}
{block name=content}
<div class="panel panel-info">
	<div class="panel-heading"><h3>[{$VAL_HOTLISTID}] {$VAL_NAME|escape}</h3></div>
	<div class="panel-body">
		{include file="HotlistProjectOptionsControl.tpl"}
		<div class="container-fluid">
			<div class="row">
				<div class="col-xs-6">
					<h4>Details</h4>
					<ul class="list-unstyled">
						<li><span class="glyphicon glyphicon-cog"></span> {$smarty.const.STR_PRJ_TOTTASKS|escape}: {$VAL_TOTALTASKS|escape}</li>
						<li><span class="glyphicon glyphicon-cog"></span> {$smarty.const.STR_PRJ_TASKSCOMP|escape}: {$VAL_TASKSCLOSED|escape}</li>
						<li><span class="glyphicon glyphicon-cog"></span> {$smarty.const.STR_PRJ_PCTCOMP|escape}: {$VAL_PCTCOMP|escape}</li>
						<li><span class="glyphicon glyphicon-user"></span> {$smarty.const.STR_PRJ_TOTRESINCOMPWO|escape}: {$VAL_RESOURCES|escape}</li>
					</ul>
				</div>
				<div class="col-xs-6">
					<h4>Hours</h4>
					<ul class="list-unstyled">
						<li><span class="glyphicon glyphicon-time"></span> {$smarty.const.STR_PRJ_HOURSPROJ|escape}: {$VAL_ESTHOURS|escape}</li>
						<li><span class="glyphicon glyphicon-time"></span> {$smarty.const.STR_PRJ_HOURSPM|escape}: {$VAL_HOURSPM|escape}</li>
						<li><span class="glyphicon glyphicon-time"></span> {$smarty.const.STR_PRJ_HOURSAPP|escape}: {$VAL_TOTALHOURS|escape}</li>
						<li><span class="glyphicon glyphicon-time"></span> {$smarty.const.STR_PRJ_HOURSREM|escape}: {$VAL_ETCHOURS|escape}</li>
					</ul>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<h4>{$smarty.const.STR_PRJ_DESCRIPTION|escape}</h4>
					<p>{$VAL_DESCRIPTION|escape|dcl_link}</p>
				</div>
			</div>
		</div>
	</div>
</div>
{dcl_modal title=Accounts}
{include file="HotlistProjectTasksControl.tpl"}
{/block}
{block name=script}
<script type="text/javascript">
	$(function() {
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
	});

	function forceSubmit(sAction)
	{
		var f = document.frmWorkorders;
		f.elements['menuAction'].value = sAction;
		processSubmit(f);
	}

	function processSubmit(f){
		var sAction = f.elements['menuAction'].value;
		if (sAction == 'WorkOrder.BatchDetail' || sAction == 'boTimecards.batchadd' || sAction == 'WorkOrder.BatchReassign')
		{
			if (!submitActionIfValid(sAction))
			{
				alert('You must select at least one work order.');
				return false;
			}
		}
		return true;
	}

	function toggleCheckGroup(btnSender)
	{
		var bChk = btnSender.checked;
		var bOK = false;
		var e=btnSender.form.elements;
		for (var i=0;i<e.length;i++){
			if (!bOK && e[i] == btnSender)
				bOK = true;
			else if (bOK && (e[i].type != "checkbox" || e[i].value == "_groupcheck_"))
				return;
			else if (bOK && e[i].type == "checkbox" && e[i].value != "_groupcheck_")
				e[i].checked = bChk;
		}
	}

	function submitActionIfValid(sAction){
		var bHasChecks = false;
		var f = document.forms['frmWorkorders'];
		for (var i = 0; i < f.elements.length && !bHasChecks; i++){
			bHasChecks = (f.elements[i].type == "checkbox" && f.elements[i].checked)
		}
		if (bHasChecks){
			f.elements['menuAction'].value = sAction;
			f.submit();
		}

		return bHasChecks;
	}

</script>
{/block}