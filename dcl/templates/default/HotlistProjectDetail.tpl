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
{include file="HotlistProjectTasksControl.tpl"}
<script type="text/javascript">

	function forceSubmit(sAction)
	{
		var f = document.frmProject;
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

	function showAccounts(iWOID, iSeq)
	{
		var sURL = 'main.php?menuAction=htmlWindowList.FrameRender&what=dcl_wo_account.wo_id&wo_id=' + iWOID + '&seq=' + iSeq;
		var newWin = window.open(sURL, '_dcl_selector_', 'width=500,height=255');
	}

</script>
