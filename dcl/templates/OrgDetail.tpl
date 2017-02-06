{extends file="_Layout.tpl"}
{block name=title}[{Org->org_id}] {Org->name|escape}{/block}
{block name=content}
<h4>Organization [{Org->org_id}] {Org->name|escape}</h4>
<div class="row">
	<div class="btn-group">
		{if $PERM_VIEW}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlOrgBrowse.show&filterActive=Y">Browse</a>{/if}
		{if $PERM_VIEW_CONTACT}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlContactBrowse.show&filterActive=Y&org_id={Org->org_id}">Contacts</a>{/if}
		{if $PERM_VIEW_WORKORDER || $PERM_VIEW_TICKET}
			{if $PERM_VIEW_WORKORDER}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlOrganizationDashboard.Show&id={Org->org_id}">Dashboard</a>{/if}
			{if $PERM_VIEW_TICKET} <a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlOrganizationDashboard.ShowTicket&id={Org->org_id}">(Tickets)</a>{/if}
			{if $PERM_VIEW_MEASUREMENT}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Organization.SlaReport&org_id={Org->org_id}">SLA</a>{/if}
			{if $PERM_VIEW_MEASUREMENT}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Organization.Measurement&org_id={Org->org_id}">Measurements</a>{/if}
			{if $PERM_VIEW_OUTAGE}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Organization.Outage&org_id={Org->org_id}">Outages</a>{/if}
		{/if}
		{if $PERM_VIEW_WORKORDER}
			<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlOrg.viewWorkOrders&id={Org->org_id}">Work Orders</a>
			<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=boWatches.add&typeid=6&whatid1={Org->org_id}">(Watch)</a>
		{/if}
		{if $PERM_VIEW_TICKET}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlOrg.viewTickets&id={Org->org_id}">Tickets</a>
			<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=boWatches.add&typeid=7&whatid1={Org->org_id}">(Watch)</a>
		{/if}
		{if $PERM_WIKI}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlWiki.show&type=5&id={Org->org_id}">Wiki</a>{/if}
		{if $PERM_MODIFY}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Organization.Edit&org_id={Org->org_id}">Edit</a>{/if}
		{if $PERM_DELETE}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Organization.Delete&org_id={Org->org_id}">Delete</a>{/if}
	</div>
</div>
<h4>Types</h4>
{section name=typeitem loop=$OrgType}
	{if $smarty.section.typeitem.first}<div class="panel panel-default"><div class="panel-body">{/if}
	<div class="badge">{$OrgType[typeitem].org_type_name|escape}</div>
	{if $smarty.section.typeitem.last}</div></div>{/if}
{/section}
<h4>{$smarty.const.STR_CM_ALIASES}Aliases{if $PERM_MODIFY} <small><a class="pull-right btn btn-success btn-xs" href="{$URL_MAIN_PHP}?menuAction=OrganizationAlias.Create&org_id={Org->org_id}" title="{$smarty.const.STR_CMMN_NEW|escape}"><span class="glyphicon glyphicon-plus"></span></a></small>{/if}</h4>
<table width="100%" class="table table-striped">
	<tbody>
{section name=aliasitem loop=$OrgAlias}
	<tr>
		<td class="string">{$OrgAlias[aliasitem].alias}</td>
{strip}
		<td class="options">
		{if $PERM_MODIFY}
			<a href="{$URL_MAIN_PHP}?menuAction=OrganizationAlias.Edit&org_id={Org->org_id}&org_alias_id={$OrgAlias[aliasitem].org_alias_id}">{$smarty.const.STR_CMMN_EDIT}</a>
			&nbsp;|&nbsp;
			<a href="javascript:;" class="dcl-delete-org-alias" data-id="{$OrgAlias[aliasitem].org_alias_id}">{$smarty.const.STR_CMMN_DELETE}</a>
		{/if}
		</td>
{/strip}
	</tr>
{/section}
	</tbody>
</table>
<h4>{$smarty.const.STR_CMMN_PRODUCTS}Products {if $PERM_MODIFY}<small><a class="pull-right btn btn-primary btn-xs" href="{$URL_MAIN_PHP}?menuAction=OrganizationProduct.Edit&org_id={Org->org_id}" title="{$smarty.const.STR_CMMN_EDIT}"><span class="glyphicon glyphicon-pencil"></span></a></small>{/if}</h4>
{section name=productitem loop=$OrgProduct}
	{if $smarty.section.productitem.first}<div class="panel panel-default"><div class="panel-body">{/if}
	<span class="badge alert-info">{$OrgProduct[productitem].name|escape}</span>
	{if $smarty.section.productitem.last}</div></div>{/if}
{/section}
<h4>{$smarty.const.STR_CM_ADDR}Addresses {if $PERM_MODIFY}<small><a class="pull-right btn btn-success btn-xs" href="{$URL_MAIN_PHP}?menuAction=OrganizationAddress.Create&org_id={Org->org_id}" title="{$smarty.const.STR_CMMN_NEW|escape}"><span class="glyphicon glyphicon-plus"></span></a></small>{/if}</h4>
<table width="100%" class="table table-striped">
	<tbody>
		{assign var="rowClass" value=""}
		{section name=address loop=$OrgAddress}
			<tr>
		<td class="string rowheader">{$OrgAddress[address].addr_type_name}{if $OrgAddress[address].preferred == "Y"} <span class="glyphicon glyphicon-flag text-info"></span>{/if}</td>
		<td class="string">
		{$OrgAddress[address].add1}<br />
		{if $OrgAddress[address].add2 != ""}
			{$OrgAddress[address].add2}<br />
		{/if}
		{$OrgAddress[address].city}, {$OrgAddress[address].state}   {$OrgAddress[address].zip}
		{if $OrgAddress[address].country != ""}
			<br />{$OrgAddress[address].country}
		{/if}
		</td>
{strip}
		<td class="options">
		{if $PERM_MODIFY}
			<a href="{$URL_MAIN_PHP}?menuAction=OrganizationAddress.Edit&org_id={Org->org_id}&org_addr_id={$OrgAddress[address].org_addr_id}">{$smarty.const.STR_CMMN_EDIT}</a>
			&nbsp;|&nbsp;
			<a href="javascript:;" class="dcl-delete-org-address" data-id="{$OrgAddress[address].org_addr_id}">{$smarty.const.STR_CMMN_DELETE}</a>
		{/if}
		</td>
		</tr>
{/strip}
		{/section}
	</tbody>
</table>
<h4>{$smarty.const.STR_CM_PHONENUMBERS}Phone Numbers {if $PERM_MODIFY}<small><a class="pull-right btn btn-success btn-xs" href="{$URL_MAIN_PHP}?menuAction=OrganizationPhone.Create&org_id={Org->org_id}" title="{$smarty.const.STR_CMMN_NEW|escape}"><span class="glyphicon glyphicon-plus"></span></a></small>{/if}</h4>
<table width="100%" class="table table-striped">
	<tbody>
{assign var="rowClass" value=""}
{section name=phone loop=$OrgPhone}
	<tr>
		<td class="rowheader">{$OrgPhone[phone].phone_type_name}{if $OrgPhone[phone].preferred == "Y"} <span class="glyphicon glyphicon-flag text-info"></span>{/if}</td>
		<td>{$OrgPhone[phone].phone_number}</td>
		<td class="options">
{strip}
		{if $PERM_MODIFY}
			<a href="{$URL_MAIN_PHP}?menuAction=OrganizationPhone.Edit&org_id={Org->org_id}&org_phone_id={$OrgPhone[phone].org_phone_id}">{$smarty.const.STR_CMMN_EDIT}</a>
			&nbsp;|&nbsp;
			<a href="javascript:;" class="dcl-delete-org-phone" data-id="{$OrgPhone[phone].org_phone_id}">{$smarty.const.STR_CMMN_DELETE}</a>
		{/if}
{/strip}
		</td>
	</tr>
{/section}
	</tbody>
</table>
<h4>{$smarty.const.STR_CM_EMAILADDRESSES}E-Mail Addresses {if $PERM_MODIFY}<small><a class="pull-right btn btn-success btn-xs" href="{$URL_MAIN_PHP}?menuAction=OrganizationEmail.Create&org_id={Org->org_id}" title="{$smarty.const.STR_CMMN_NEW|escape}"><span class="glyphicon glyphicon-plus"></span></a></small>{/if}</h4>
<table width="100%" class="table table-striped">
	<tbody>
{section name=email loop=$OrgEmail}
	<tr>
		<td class="rowheader">{$OrgEmail[email].email_type_name}{if $OrgEmail[email].preferred == "Y"} <span class="glyphicon glyphicon-flag text-info"></span>{/if}</td>
		<td>{mailto address=$OrgEmail[email].email_addr}</td>
		<td class="options">
{strip}
		{if $PERM_MODIFY}
			<a href="{$URL_MAIN_PHP}?menuAction=OrganizationEmail.Edit&org_id={Org->org_id}&org_email_id={$OrgEmail[email].org_email_id}">{$smarty.const.STR_CMMN_EDIT}</a>
			&nbsp;|&nbsp;
			<a href="javascript:;" class="dcl-delete-org-email" data-id="{$OrgEmail[email].org_email_id}">{$smarty.const.STR_CMMN_DELETE}</a>
		{/if}
{/strip}
		</td>
	</tr>
{/section}
	</tbody>
</table>
<h4>Environments {if $PERM_MODIFY}<small><a class="pull-right btn btn-success btn-xs" href="{$URL_MAIN_PHP}?menuAction=EnvironmentOrg.Create&org_id={Org->org_id}" title="{$smarty.const.STR_CMMN_NEW|escape}"><span class="glyphicon glyphicon-plus"></span></a></small>{/if}</h4>
<table width="100%" class="table table-striped">
	<tbody>
	{section name=env loop=$OrgEnvironment}
		<tr>
			<td class="rowheader">{$OrgEnvironment[env].environment_name|escape}{if $OrgEnvironment[env].end_dt == ""} <span class="glyphicon glyphicon-flag text-info"></span>{/if}</td>
			<td>{$OrgEnvironment[env].begin_dt}{if $OrgEnvironment[env].end_dt != ""} &mdash; {$OrgEnvironment[env].end_dt}{/if}</td>
			<td class="options">
				{strip}
					{if $PERM_MODIFY}
						<a href="{$URL_MAIN_PHP}?menuAction=EnvironmentOrg.Edit&environment_org_id={$OrgEnvironment[env].environment_org_id}">{$smarty.const.STR_CMMN_EDIT|escape}</a>
						&nbsp;|&nbsp;
						<a href="javascript:;" class="dcl-delete-environment-org" data-id="{$OrgEnvironment[env].environment_org_id}">{$smarty.const.STR_CMMN_DELETE|escape}</a>
					{/if}
				{/strip}
			</td>
		</tr>
	{/section}
	</tbody>
</table>
<h4>Uptime SLA {if $PERM_MODIFY}<small><a class="pull-right btn btn-primary btn-xs" href="{$URL_MAIN_PHP}?menuAction=OrganizationOutageSla.Edit&org_id={Org->org_id}" title="{$smarty.const.STR_CMMN_EDIT|escape}"><span class="glyphicon glyphicon-pencil"></span></a></small>{/if}</h4>
<div class="panel panel-default"><div class="panel-body">
{if $OrgOutage != null && $OrgOutage->outage_sla > 0}
	This organization has an uptime requirement of {$OrgOutage->outage_sla}%{if $OrgOutage->outage_sla_warn > 0} with a warning at {$OrgOutage->outage_sla_warn}%{/if}.
{else}
	No uptime SLA has been defined for this organization.
{/if}
</div></div>
<h4>Measurement SLA {if $PERM_MODIFY}<small><a class="pull-right btn btn-success btn-xs" href="{$URL_MAIN_PHP}?menuAction=OrganizationMeasurementSla.Create&org_id={Org->org_id}" title="{$smarty.const.STR_CMMN_NEW|escape}"><span class="glyphicon glyphicon-plus"></span></a></small>{/if}</h4>
<div class="panel panel-default"><div class="panel-body">
{section name=measurement loop=$OrgMeasureSla}
	{if $smarty.section.measurement.first}<table class="table"><thead><tr><th>Measurement</th><th>Unit</th><th>Min</th><th>Max</th><th>SLA</th><th>Warn At</th><th>Trim</th><th>SLA Is Trim</th><th>Schedule</th></tr></thead><tbody>{/if}
	<tr>
		<td>{$OrgMeasureSla[measurement].type|escape}</td>
		<td>{$OrgMeasureSla[measurement].unit|escape}</td>
		<td>{$OrgMeasureSla[measurement].min|escape}</td>
		<td>{$OrgMeasureSla[measurement].max|escape}</td>
		<td>{$OrgMeasureSla[measurement].sla|escape}</td>
		<td>{$OrgMeasureSla[measurement].warn|escape}</td>
		<td>{$OrgMeasureSla[measurement].trimPct|escape}%</td>
		<td>{$OrgMeasureSla[measurement].slaIsTrim|escape}</td>
		<td>{$OrgMeasureSla[measurement].schedule|escape}</td>
	</tr>
	{if $smarty.section.measurement.last}</tbody></table>{/if}
{sectionelse}
	No measurement SLA has been defined for this organization.
{/section}
</div></div>
<h4>{$smarty.const.STR_CM_URL}URLs {if $PERM_MODIFY}<small><a class="pull-right btn btn-success btn-xs" href="{$URL_MAIN_PHP}?menuAction=OrganizationUrl.Create&org_id={Org->org_id}" title="{$smarty.const.STR_CMMN_NEW|escape}"><span class="glyphicon glyphicon-plus"></span></a></small>{/if}</h4>
<table width="100%" class="table table-striped">
	<tbody>
{section name=url loop=$OrgURL}
	<tr>
		<td class="rowheader">{$OrgURL[url].url_type_name}{if $OrgURL[url].preferred == "Y"} <span class="glyphicon glyphicon-flag text-info"></span>{/if}</td>
		<td>{$OrgURL[url].url_addr|escape|dcl_link}</td>
		<td class="options">
{strip}
		{if $PERM_MODIFY}
			<a href="{$URL_MAIN_PHP}?menuAction=OrganizationUrl.Edit&org_id={Org->org_id}&org_url_id={$OrgURL[url].org_url_id}">{$smarty.const.STR_CMMN_EDIT}</a>
			&nbsp;|&nbsp;
			<a href="javascript:;" class="dcl-delete-org-url" data-id="{$OrgURL[url].org_url_id}">{$smarty.const.STR_CMMN_DELETE}</a>
		{/if}
{/strip}
		</td>
	</tr>
{/section}
	</tbody>
</table>
{if $PERM_VIEW_CONTACT}
<h4>{$smarty.const.STR_CMMN_CONTACTS}Contacts</h4>
<table width="100%" class="table table-striped">
	<thead>
		<tr><th>{$smarty.const.STR_CMMN_NAME}</th><th>{$smarty.const.STR_CMMN_TYPE}Type</th><th>Phone</th><th>Email</th><th>URL</th></tr>
	</thead>
	<tbody>
{section name=contact loop=$OrgContacts}
	<tr>
		<td><a href="{$URL_MAIN_PHP}?menuAction=htmlContactDetail.show&contact_id={$OrgContacts[contact].contact_id}">{$OrgContacts[contact].name}</a></td>
		<td>{$OrgContacts[contact].type|escape}</td>
		<td>{$OrgContacts[contact].phone|escape}</td>
		<td>{$OrgContacts[contact].email|escape|dcl_link}</td>
		<td>{$OrgContacts[contact].url|escape|dcl_link}</td>
	</tr>
{/section}
	</tbody>
</table>
{/if}
{if $PERM_VIEW_TICKET}
<h4>{$smarty.const.STR_CM_LAST10TICKETS}Last 10 Tickets {if $PERM_MODIFY}<small><a class="pull-right btn btn-success btn-xs" href="{$URL_MAIN_PHP}?menuAction=boTickets.add&org_id={Org->org_id}" title="{$smarty.const.STR_CMMN_NEW|escape}"><span class="glyphicon glyphicon-plus"></span></a></small>{/if}</h4>
<table width="100%" class="table table-striped">
	<thead>
		<tr>{foreach item=col from=$ViewTicket->columnhdrs}<th>{$col}</th>{/foreach}</tr>
	</thead>
	<tbody>
{foreach name=tickets item=record from=$Tickets}
	<tr>
	{foreach name=ticket key=key item=data from=$record}
	{if is_numeric($key)}<td>{if $smarty.foreach.ticket.first}<a href="{$URL_MAIN_PHP}?menuAction=boTickets.view&ticketid={$record.ticketid}">{$data}</a>{else}{$data}{/if}</td>{/if}
	{/foreach}
	</tr>
{/foreach}
	</tbody>
</table>
{/if}
{if $PERM_VIEW_WORKORDER}
<h4>{$smarty.const.STR_CM_LAST10WORKORDERS}Last 10 Work Orders {if $PERM_MODIFY}<small><a class="pull-right btn btn-success btn-xs" href="{$URL_MAIN_PHP}?menuAction=WorkOrder.CreateForOrg&org_id={Org->org_id}" title="{$smarty.const.STR_CMMN_NEW|escape}"><span class="glyphicon glyphicon-plus"></span></a></small>{/if}</h4>
<table width="100%" class="table table-striped">
	<thead>
		<tr>{foreach item=col from=$ViewWorkOrder->columnhdrs}<th>{$col}</th>{/foreach}</tr>
	</thead>
	<tbody>
{foreach name=workorders item=record from=$WorkOrders}
	<tr>
	{foreach name=wo item=data key=key from=$record}
	{if is_numeric($key)}
	<td>{if $smarty.foreach.wo.iteration < 3}<a href="{$URL_MAIN_PHP}?menuAction=WorkOrder.Detail&jcn={$record.jcn}&seq={$record.seq}">{$data}</a>{else}{$data}{/if}</td>
	{/if}
	{/foreach}
	</tr>
{/foreach}
	</tbody>
</table>
{/if}
{/block}
{block name=script}
<script type="text/javascript">
	$(document).ready(function() {
		var urlMainPhp = "{$URL_MAIN_PHP}";
		var orgId = {Org->org_id};
		$(".dcl-delete-org-address").click(function() {
			if (confirm("Are you sure you want to delete this address?")) {
				var id = $(this).attr("data-id");
				var $row = $(this).parents("tr:first");
				$.ajax({
					type: "POST",
					url: urlMainPhp,
					data: { menuAction: "OrganizationAddress.Destroy", org_id: orgId, org_addr_id: id },
					success: function() {
						$row.remove();
						$.gritter.add({
							title: "Success",
							text: "Address deleted successfully."
						});
					},
					error: function() {
						$.gritter.add({ title: "Error", text: "Address was not deleted successfully." });
					}
				});
			}
		});

		$(".dcl-delete-org-alias").click(function() {
			if (confirm("Are you sure you want to delete this alias?")) {
				var id = $(this).attr("data-id");
				var $row = $(this).parents("tr:first");
				$.ajax({
					type: "POST",
					url: urlMainPhp,
					data: { menuAction: "OrganizationAlias.Destroy", org_id: orgId, org_alias_id: id },
					success: function() {
						$row.remove();
						$.gritter.add({
							title: "Success",
							text: "Alias deleted successfully."
						});
					},
					error: function() {
						$.gritter.add({ title: "Error", text: "Alias was not deleted successfully." });
					}
				});
			}
		});

		$(".dcl-delete-org-email").click(function() {
			if (confirm("Are you sure you want to delete this email?")) {
				var id = $(this).attr("data-id");
				var $row = $(this).parents("tr:first");
				$.ajax({
					type: "POST",
					url: urlMainPhp,
					data: { menuAction: "OrganizationEmail.Destroy", org_id: orgId, org_email_id: id },
					success: function() {
						$row.remove();
						$.gritter.add({
							title: "Success",
							text: "Email deleted successfully."
						});
					},
					error: function() {
						$.gritter.add({ title: "Error", text: "Email was not deleted successfully." });
					}
				});
			}
		});

		$(".dcl-delete-org-phone").click(function() {
			if (confirm("Are you sure you want to delete this phone number?")) {
				var id = $(this).attr("data-id");
				var $row = $(this).parents("tr:first");
				$.ajax({
					type: "POST",
					url: urlMainPhp,
					data: { menuAction: "OrganizationPhone.Destroy", org_id: orgId, org_phone_id: id },
					success: function() {
						$row.remove();
						$.gritter.add({
							title: "Success",
							text: "Phone number deleted successfully."
						});
					},
					error: function() {
						$.gritter.add({ title: "Error", text: "Phone number was not deleted successfully." });
					}
				});
			}
		});

		$(".dcl-delete-environment-org").click(function() {
			if (confirm("Are you sure you want to delete this environment?")) {
				var id = $(this).attr("data-id");
				var $row = $(this).parents("tr:first");
				$.ajax({
					type: "POST",
					url: urlMainPhp,
					data: { menuAction: "EnvironmentOrg.Destroy", environment_org_id: id },
					success: function() {
						$row.remove();
						$.gritter.add({
							title: "Success",
							text: "Environment deleted successfully."
						});
					},
					error: function() {
						$.gritter.add({ title: "Error", text: "Environment was not deleted successfully." });
					}
				});
			}
		});

		$(".dcl-delete-org-url").click(function() {
			if (confirm("Are you sure you want to delete this URL?")) {
				var id = $(this).attr("data-id");
				var $row = $(this).parents("tr:first");
				$.ajax({
					type: "POST",
					url: urlMainPhp,
					data: { menuAction: "OrganizationUrl.Destroy", org_id: orgId, org_url_id: id },
					success: function() {
						$row.remove();
						$.gritter.add({
							title: "Success",
							text: "URL deleted successfully."
						});
					},
					error: function() {
						$.gritter.add({ title: "Error", text: "URL was not deleted successfully." });
					}
				});
			}
		});
	});
</script>
{/block}