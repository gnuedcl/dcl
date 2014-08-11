{if $PERM_MODIFY_TC && $VAL_EDITTCID}
{dcl_validator_init}
<script type="text/javascript">
function validateAndSubmitForm(form)
{
	var aValidators = [
			new ValidatorDate(form.elements["actionon"], "{$smarty.const.STR_TC_DATE}"),
			new ValidatorSelection(form.elements["action"], "{$smarty.const.STR_TC_ACTION}"),
			new ValidatorSelection(form.elements["status"], "{$smarty.const.STR_TC_STATUS}"),
			new ValidatorDecimal(form.elements["hours"], "{$smarty.const.STR_TC_HOURS}"),
			new ValidatorString(form.elements["summary"], "{$smarty.const.STR_WO_SUMMARY}")
		];

	for (var i in aValidators)
	{
		if (!aValidators[i].isValid())
		{
			alert(aValidators[i].getError());
			if (typeof(aValidators[i]._Element.focus) == "function")
				aValidators[i]._Element.focus();
			return;
		}
	}

	form.submit();
}

</script>
{/if}
<script type="text/javascript">
	$(function() {
		$("input[data-input-type=date]").datepicker();
		var hash = location.hash;
		if (hash) {
			var $tabs = $('#tabs').find('a[href="' + hash + '"]');
			$tabs.tab('show');
		}

		$("body").on("click", "a.remove-from-project", function() {
			var $link = $(this);
			var jcn = $link.attr("data-jcn");
			var seq = $link.attr("data-seq");

			if (!confirm("Are you sure you want to remove this work order from its project?"))
				return;

			$.ajax({
				type: "POST",
				url: "{$URL_MAIN_PHP}?menuAction=Project.RemoveTask",
				data: { "jcn": jcn, "seq": seq },
				success: function() {
					$.gritter.add({ title: "Success", text: "Successfully removed from project." });
					$("div.panel").find("div.project-item-" + jcn + "-" + seq).hide("slow", function() { $(this).remove(); });
				},
				error: function() {
					$.gritter.add({ title: "Error", text: "Could not remove the work order from its project." });
				}
			});
		});
	});

function submitAction(sFormName, sAction)
{
	var oForm = document.getElementById(sFormName);
	if (!oForm)
		return;
		
	oForm.menuAction.value = sAction;
	oForm.submit();
}

</script>
<div class="panel panel-info">
	<div class="panel-heading"><h3>[{$VAL_JCN}-{$VAL_SEQ}] {$VAL_SUMMARY|escape}</h3></div>
	<div class="panel-body">
		<ul id="tabs" class="nav nav-tabs">
			<li class="active"><a href="#workorder" data-toggle="tab">Work Order</a></li>
			<li><a href="#files" data-toggle="tab">Files <span class="badge{if count($VAL_ATTACHMENTS) > 0} alert-info{/if}">{$VAL_ATTACHMENTS|@count}</span></a></li>
			{include file="WorkOrderOptionsControl.tpl"}
		</ul>
		<div id="navTabContent" class="tab-content">
			<div class="tab-pane fade in active" id="workorder">
				<div class="container-fluid">
					<div class="row">
						<div class="col-xs-6">
							<h4>Details</h4>
							<ul class="list-unstyled">
								<li><span class="glyphicon glyphicon-cog"></span> {$VAL_PRODUCT|escape} {$VAL_MODULE|escape}</li>
								<li><span class="glyphicon glyphicon-stats"></span> <strong class="status-type-{$VAL_STATUS_TYPE}">{$VAL_STATUS|escape}</strong> {$VAL_STATUSON|escape}</li>
								<li><span class="glyphicon glyphicon-sort-by-order"></span> {$VAL_PRIORITY|escape}</li>
								<li><span class="glyphicon glyphicon-flash"></span> {$VAL_SEVERITY|escape}</li>
								<li><span class="glyphicon glyphicon-asterisk"></span> {$VAL_TYPE|escape}</li>
								{if $VAL_REPORTED_VERSION}<li><span class="glyphicon glyphicon-asterisk"></span> Reported Version {$VAL_REPORTED_VERSION|escape}</li>{/if}
								{if $VAL_TARGETED_VERSION}<li><span class="glyphicon glyphicon-asterisk"></span> Targeted Version {$VAL_TARGETED_VERSION|escape}</li>{/if}
								{if $VAL_FIXED_VERSION}<li><span class="glyphicon glyphicon-asterisk"></span> Fixed Version {$VAL_FIXED_VERSION|escape}</li>{/if}
							</ul>
						</div>
						<div class="col-xs-6">
							<h4>Dates and Times</h4>
							<ul class="list-unstyled">
								<li><span class="glyphicon glyphicon-bullhorn"></span> {$smarty.const.STR_WO_OPENBY} {dcl_personnel_link text=$VAL_CREATEBY id=$WorkOrder->createby} on {$VAL_CREATEDON|escape}</li>
								{if $VAL_STATUS_TYPE == 2}<li><span class="glyphicon glyphicon-flag"></span> {$smarty.const.STR_WO_CLOSEBY} {dcl_personnel_link text=$VAL_CLOSEDBY id=$WorkOrder->closedby} on {$VAL_CLOSEDON|escape}</li>{/if}
								<li><span class="glyphicon glyphicon-calendar"></span> {$smarty.const.STR_WO_LASTACTION} {$VAL_LASTACTIONON|escape}</li>
							</ul>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							{if $VAL_CONTACTID}
								<h4>{$smarty.const.STR_WO_CONTACT|escape}</h4>
								<div>
									<span class="glyphicon glyphicon-user"></span> {if $PERM_VIEWCONTACT}<a href="{$URL_MAIN_PHP}?menuAction=htmlContactDetail.show&contact_id={$VAL_CONTACTID}">{/if}{$VAL_CONTACT|escape}{if $PERM_VIEWCONTACT}</a>{/if}
									{if $VAL_CONTACTEMAIL != ""}<span class="glyphicon glyphicon-envelope"></span> {mailto address=$VAL_CONTACTEMAIL}{/if}
									{if $VAL_CONTACTPHONE != ""}<span class="glyphicon glyphicon-phone"></span> {$VAL_CONTACTPHONE|escape}{/if}
								</div>

							{/if}
							{if count($VAL_ORGS) > 0}
								<h4>{$smarty.const.STR_CMMN_ORGANIZATION} <span class="badge alert-info">{$VAL_ORGS|@count}</span></h4>
								{section name=org loop=$VAL_ORGS}
									{if $PERM_VIEWORG}<a href="{$URL_MAIN_PHP}?menuAction=Organization.Detail&org_id={$VAL_ORGS[org].org_id}">{/if}{$VAL_ORGS[org].org_name|escape}{if $PERM_VIEWORG}</a>{/if}
									{if !$smarty.section.org.last},&nbsp;{/if}
								{/section}
							{/if}
							<h4>{$smarty.const.STR_WO_DESCRIPTION|escape}</h4>
							<p>{$VAL_DESCRIPTION|escape|dcl_link}</p>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="files">
				{include file="AttachmentsControl.tpl"}
			</div>
		</div>
	</div>
</div>
{include file="TimeCardsControl.tpl"}
