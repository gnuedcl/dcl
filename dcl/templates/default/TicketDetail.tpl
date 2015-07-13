{if $PERM_MODIFY_TR && $VAL_EDITRESID}
{dcl_validator_init}
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
<script type="text/javascript">
function validateAndSubmitForm(form) {
	var aValidators = [
			new ValidatorSelection(form.elements["status"], "{$smarty.const.STR_TCK_STATUS}"),
			new ValidatorString(form.elements["resolution"], "{$smarty.const.STR_TCK_RESOLUTION}")
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
<script type="text/javascript" src="{$DIR_VENDOR}readmore/readmore.min.js"></script>
<script type="text/javascript">
function submitAction(sAction) {
	document.actionForm.menuAction.value = sAction;
	document.actionForm.submit();
}

$(function() {
	var hash = location.hash;
	if (hash) {
		var $tabs = $('#tabs').find('a[href="' + hash + '"]');
		$tabs.tab('show');
	}

	$("#issue").readmore({
		moreLink: '<a href="javascript:;" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-collapse-down"></span> Show More...</a>',
		lessLink: '<a href="javascript:;" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-collapse-up"></span> Show Less...</a>'
	});
});
</script>
<div class="panel panel-info">
	<div class="panel-heading"><h3>[{$VAL_TICKETID}] {$VAL_SUMMARY|escape}</h3></div>
	<div class="panel-body">
		<ul id="tabs" class="nav nav-tabs">
			<li class="active"><a href="#workorder" data-toggle="tab">Ticket</a></li>
			<li><a href="#files" data-toggle="tab">Files <span class="badge{if count($VAL_ATTACHMENTS) > 0} alert-info{/if}">{$VAL_ATTACHMENTS|@count}</span></a></li>
			{include file="TicketOptionsControl.tpl"}
		</ul>
		<div id="navTabContent" class="tab-content">
			<div class="tab-pane fade in active" id="workorder">
				<div class="container-fluid">
					<div class="row">
						<div class="col-xs-6">
							<h4>Details</h4>
							<ul class="list-unstyled">
								<li><span class="glyphicon glyphicon-cog"></span> {$VAL_PRODUCT|escape} {$VAL_MODULE|escape}</li>
								<li><span class="glyphicon glyphicon-user"></span> {dcl_personnel_link text=$VAL_RESPONSIBLE id=$Ticket->responsible}</li>
								<li><span class="glyphicon glyphicon-stats"></span> <strong class="status-type-{$VAL_STATUS_TYPE}">{$VAL_STATUS|escape}</strong> {$VAL_STATUSON|escape}</li>
								<li><span class="glyphicon glyphicon-sort-by-order"></span> {$VAL_PRIORITY|escape}</li>
								<li><span class="glyphicon glyphicon-asterisk"></span> {$VAL_TYPE|escape}</li>
								<li><span class="glyphicon glyphicon-{if $VAL_PUBLIC == $smarty.const.STR_CMMN_YES}eye-open{else}lock{/if}"></span> {if $VAL_PUBLIC == $smarty.const.STR_CMMN_YES}Public{else}Private{/if}</li>
								{if $VAL_VERSION}<li><span class="glyphicon glyphicon-asterisk"></span> Reported Version {$VAL_VERSION|escape}</li>{/if}
							</ul>
						</div>
						<div class="col-xs-6">
							<h4>Dates and Times</h4>
							<ul class="list-unstyled">
								<li><span class="glyphicon glyphicon-bullhorn"></span> {$smarty.const.STR_TCK_OPENBY} {dcl_personnel_link text=$VAL_CREATEDBY id=$Ticket->createdby} on {$VAL_CREATEDON|escape}</li>
								{if $VAL_STATUS_TYPE == 2}<li><span class="glyphicon glyphicon-flag"></span> {$smarty.const.STR_TCK_CLOSEBY} {dcl_personnel_link text=$VAL_CLOSEDBY id=$Ticket->closedby} on {$VAL_CLOSEDON|escape}</li>{/if}
								<li><span class="glyphicon glyphicon-time"></span> {if $VAL_HOURSTEXT != ""}{$VAL_HOURSTEXT|escape}{else}0{/if} Hours</li>
								<li><span class="glyphicon glyphicon-calendar"></span> {$smarty.const.STR_TCK_LASTACTION} {$VAL_LASTACTIONON|escape}</li>
							</ul>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							{if $VAL_TAGS}<div><span class="glyphicon glyphicon-tag"></span> {dcl_tag_link value=$VAL_TAGS}</div>{/if}
							{if $VAL_CONTACTID}
								<h4>{$smarty.const.STR_TCK_CONTACT|escape}</h4>
								<div>
									<span class="glyphicon glyphicon-user"></span> {if $PERM_VIEWCONTACT}<a href="{$URL_MAIN_PHP}?menuAction=htmlContactDetail.show&contact_id={$VAL_CONTACTID}">{/if}{$VAL_CONTACT|escape}{if $PERM_VIEWCONTACT}</a>{/if}
									{if $VAL_CONTACTEMAIL != ""}<span class="glyphicon glyphicon-envelope"></span> {mailto address=$VAL_CONTACTEMAIL}{/if}
									{if $VAL_CONTACTPHONE != ""}<span class="glyphicon glyphicon-phone"></span> {$VAL_CONTACTPHONE|escape}{/if}
								</div>

							{/if}
							{if $VAL_ORGID}
								<h4>{$smarty.const.STR_CMMN_ORGANIZATION}</h4>
								<a href="{$URL_MAIN_PHP}?menuAction=Organization.Detail&org_id={$VAL_ORGID}">{$VAL_ACCOUNT|escape}</a>
							{/if}
							<h4>{$smarty.const.STR_TCK_ISSUE|escape}</h4>
							<p id="issue">{$VAL_ISSUE|escape|dcl_link}</p>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="files">
				{include file="AttachmentsTicketsControl.tpl"}
			</div>
		</div>
	</div>
</div>
{include file="TicketResolutionsControl.tpl"}