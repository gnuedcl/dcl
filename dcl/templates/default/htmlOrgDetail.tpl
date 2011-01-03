<script language="JavaScript">
{literal}
	$(document).ready(function() {
		var urlMainPhp = {/literal}"{$URL_MAIN_PHP}"{literal};
		var orgId = {/literal}{Org->org_id}{literal};
		$(".dcl-delete-org-address").click(function() {
			if (confirm("Are you sure you want to delete this address?")) {
				var id = $(this).attr("data-id");
				var $row = $(this).parents("tr:first");
				$.ajax({
					type: "POST",
					url: urlMainPhp,
					data: {menuAction: "OrganizationAddress.Destroy", org_id: orgId, org_addr_id: id},
					success: function() {
						$row.remove();
						$.gritter.add({
							title: "Success",
							text: "Address deleted successfully."
						});
					},
					error: function() {
						$.gritter.add({title: "Error", text: "Address was not deleted successfully."});
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
					data: {menuAction: "OrganizationAlias.Destroy", org_id: orgId, org_alias_id: id},
					success: function() {
						$row.remove();
						$.gritter.add({
							title: "Success",
							text: "Alias deleted successfully."
						});
					},
					error: function() {
						$.gritter.add({title: "Error", text: "Alias was not deleted successfully."});
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
					data: {menuAction: "OrganizationEmail.Destroy", org_id: orgId, org_email_id: id},
					success: function() {
						$row.remove();
						$.gritter.add({
							title: "Success",
							text: "Email deleted successfully."
						});
					},
					error: function() {
						$.gritter.add({title: "Error", text: "Email was not deleted successfully."});
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
					data: {menuAction: "OrganizationPhone.Destroy", org_id: orgId, org_phone_id: id},
					success: function() {
						$row.remove();
						$.gritter.add({
							title: "Success",
							text: "Phone number deleted successfully."
						});
					},
					error: function() {
						$.gritter.add({title: "Error", text: "Phone number was not deleted successfully."});
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
					data: {menuAction: "OrganizationUrl.Destroy", org_id: orgId, org_url_id: id},
					success: function() {
						$row.remove();
						$.gritter.add({
							title: "Success",
							text: "URL deleted successfully."
						});
					},
					error: function() {
						$.gritter.add({title: "Error", text: "URL was not deleted successfully."});
					}
				});
			}
		});
	});
{/literal}
</script>
<table width="100%" class="dcl_results">
	<caption>Organization [{Org->org_id}] {Org->name}</caption>
	<thead>
		<tr class="toolbar"><th>
			<ul>
				<li class="first"><a href="{$URL_MAIN_PHP}?menuAction=htmlOrgBrowse.show&filterActive=Y">Browse</a></li>
				<li><a href="{$URL_MAIN_PHP}?menuAction=htmlContactBrowse.show&filterActive=Y&org_id={Org->org_id}">Contacts</a></li>
				<li><a href="{$URL_MAIN_PHP}?menuAction=htmlOrganizationDashboard.Show&id={Org->org_id}">Dashboard</a> (<a href="{$URL_MAIN_PHP}?menuAction=htmlOrganizationDashboard.ShowTicket&id={Org->org_id}">Tickets</a>)</li>
				<li>
					<a href="{$URL_MAIN_PHP}?menuAction=htmlOrg.viewWorkOrders&id={Org->org_id}">Work Orders</a>
					&nbsp;(<a href="{$URL_MAIN_PHP}?menuAction=boWatches.add&typeid=6&whatid1={Org->org_id}">Watch</a>)
				</li>
				<li><a href="{$URL_MAIN_PHP}?menuAction=htmlOrg.viewTickets&id={Org->org_id}">Tickets</a>
					&nbsp;(<a href="{$URL_MAIN_PHP}?menuAction=boWatches.add&typeid=7&whatid1={Org->org_id}">Watch</a>)
				</li>
				<li><a href="{$URL_MAIN_PHP}?menuAction=htmlWiki.show&type=5&id={Org->org_id}">Wiki</a></li>
				<li><a href="{$URL_MAIN_PHP}?menuAction=htmlOrgForm.modify&org_id={Org->org_id}">Edit</a></li>
				<li><a href="{$URL_MAIN_PHP}?menuAction=htmlOrgForm.delete&org_id={Org->org_id}">Delete</a></li>
			</ul>
		</th></tr>
	</thead>
	<tbody>
		<tr><td class="string">
{section name=typeitem loop=$OrgType}
{$OrgType[typeitem].org_type_name}{if !$smarty.section.typeitem.last},&nbsp;{/if}
{sectionelse}
No organization types!
{/section}
		</td></tr>
	</tbody>
</table>
<table width="100%" class="dcl_results">
	<caption class="spacer">{$smarty.const.STR_CM_ALIASES}Aliases</caption>
	<thead><tr class="toolbar"><th colspan="2"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=OrganizationAlias.Create&org_id={Org->org_id}">{$smarty.const.STR_CMMN_NEW}</a></li></ul></th></tr></thead>
	<tbody>
{section name=aliasitem loop=$OrgAlias}
{cycle values="odd,even" assign="rowClass"}
	<tr class="{$rowClass}">
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
<table width="100%" class="dcl_results">
	<caption class="spacer">{$smarty.const.STR_CMMN_PRODUCTS}Products</caption>
	{if $PERM_MODIFY}<thead><tr class="toolbar"><th colspan="2"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=OrganizationProduct.Edit&org_id={Org->org_id}">{$smarty.const.STR_CMMN_EDIT}</a></li></ul></th></tr></thead>{/if}
	<tbody>
{section name=productitem loop=$OrgProduct}
{cycle values="odd,even" assign="rowClass"}
	<tr class="{$rowClass}"><td class="string">{$OrgProduct[productitem].name}</td></tr>
{/section}
	</tbody>
</table>
<table width="100%" class="dcl_results">
	<caption class="spacer">{$smarty.const.STR_CM_ADDR}Addresses</caption>
	<thead>
		<tr class="toolbar"><th colspan="3"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=OrganizationAddress.Create&org_id={Org->org_id}">{$smarty.const.STR_CMMN_NEW}</a></li></ul></th></tr>
	</thead>
	<tbody>
		{assign var="rowClass" value=""}
		{section name=address loop=$OrgAddress}
		{cycle values="odd,even" assign="rowClass"}
		<tr class="{$rowClass}">
		<td class="string rowheader">{$OrgAddress[address].addr_type_name}{if $OrgAddress[address].preferred == "Y"}<span style="color:#a00000;">*</span>{/if}</td>
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
<table width="100%" class="dcl_results">
	<caption class="spacer">{$smarty.const.STR_CM_PHONENUMBERS}Phone Numbers</caption>
	<thead>
		<tr class="toolbar"><th colspan="3"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=OrganizationPhone.Create&org_id={Org->org_id}">{$smarty.const.STR_CMMN_NEW}</a></li></ul></th></tr>
	</thead>
	<tbody>
{assign var="rowClass" value=""}
{section name=phone loop=$OrgPhone}
		{cycle values="odd,even" assign="rowClass"}
		<tr class="{$rowClass}">
		<td class="rowheader">{$OrgPhone[phone].phone_type_name}{if $OrgPhone[phone].preferred == "Y"}<span style="color:#a00000;">*</span>{/if}</td>
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
<table width="100%" class="dcl_results">
	<caption class="spacer">{$smarty.const.STR_CM_EMAILADDRESSES}E-Mail Addresses</caption>
	<thead><tr class="toolbar"><th colspan="3"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=OrganizationEmail.Create&org_id={Org->org_id}">{$smarty.const.STR_CMMN_NEW}</a></li></ul></th></tr></thead>
	<tbody>
{section name=email loop=$OrgEmail}
		{cycle values="odd,even" assign="rowClass"}
		<tr class="{$rowClass}">
		<td class="rowheader">{$OrgEmail[email].email_type_name}{if $OrgEmail[email].preferred == "Y"}<span style="color:#a00000;">*</span>{/if}</td>
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
<table width="100%" class="dcl_results">
	<caption class="spacer">{$smarty.const.STR_CM_URL}URLs</caption>
	<thead><tr class="toolbar"><th colspan="3"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=OrganizationUrl.Create&org_id={Org->org_id}">{$smarty.const.STR_CMMN_NEW}</a></li></ul></th></tr></thead>
	<tbody>
{section name=url loop=$OrgURL}
		{cycle values="odd,even" assign="rowClass"}
	<tr class="{$rowClass}">
		<td class="rowheader">{$OrgURL[url].url_type_name}{if $OrgURL[url].preferred == "Y"}<span style="color:#a00000;">*</span>{/if}</td>
		<td>{$OrgURL[url].url_addr|escape:link}</td>
		<td class="options">
{strip}
		{if $PERM_MODIFY}
			<a href="{$URL_MAIN_PHP}?menuAction=OrganizationUrl.Edit&org_id={Org->org_id}&org_url_id={$OrgURL[url].org_url_id}">{$smarty.const.STR_CMMN_EDIT}</a>
			&nbsp;|&nbsp;
			<a href="javascript:;" class="dcl-delete-org-url" data-id="{$OrgURL[url].org_url_id}"">{$smarty.const.STR_CMMN_DELETE}</a>
		{/if}
{/strip}
		</td>
	</tr>
{/section}
	</tbody>
</table>
<table width="100%" class="dcl_results">
	<caption class="spacer">{$smarty.const.STR_CMMN_CONTACTS}Contacts</caption>
	<thead>
		<tr><th>{$smarty.const.STR_CMMN_NAME}</th><th>{$smarty.const.STR_CMMN_TYPE}Type</th><th>Phone</th><th>Email</th><th>URL</th></tr>
	</thead>
	<tbody>
{section name=contact loop=$OrgContacts}
		{cycle values="odd,even" assign="rowClass"}
	<tr class="{$rowClass}">
		<td><a href="{$URL_MAIN_PHP}?menuAction=htmlContactDetail.show&contact_id={$OrgContacts[contact].contact_id}">{$OrgContacts[contact].name}</a></td>
		<td>{$OrgContacts[contact].type|escape}</td>
		<td>{$OrgContacts[contact].phone|escape}</td>
		<td>{$OrgContacts[contact].email|escape:link}</td>
		<td>{$OrgContacts[contact].url|escape:link}</td>
	</tr>
{/section}
	</tbody>
</table>
<table width="100%" class="dcl_results">
	<caption class="spacer">{$smarty.const.STR_CM_LAST10TICKETS}Last 10 Tickets</caption>
	<thead><tr class="toolbar"><th colspan="7"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=boTickets.add&org_id={Org->org_id}">{$smarty.const.STR_CMMN_NEW}</a></li></ul></th></tr>
		<tr>{foreach item=col from=$ViewTicket->columnhdrs}<th>{$col}</th>{/foreach}</tr>
	</thead>
	<tbody>
{foreach name=tickets item=record from=$Tickets}
	{cycle values="odd,even" assign="rowClass"}
	<tr class="{$rowClass}">
	{foreach name=ticket key=key item=data from=$record}
	{if is_numeric($key)}<td>{if $smarty.foreach.ticket.first}<a href="{$URL_MAIN_PHP}?menuAction=boTickets.view&ticketid={$record.ticketid}">{$data}</a>{else}{$data}{/if}</td>{/if}
	{/foreach}
	</tr>
{/foreach}
	</tbody>
</table>
<table width="100%" class="dcl_results">
	<caption class="spacer">{$smarty.const.STR_CM_LAST10WORKORDERS}Last 10 Work Orders</caption>
	<thead><tr class="toolbar"><th colspan="9"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=boWorkorders.newjcn&org_id={Org->org_id}">{$smarty.const.STR_CMMN_NEW}</a></li></ul></th></tr>
		<tr>{foreach item=col from=$ViewWorkOrder->columnhdrs}<th>{$col}</th>{/foreach}</tr>
	</thead>
	<tbody>
{foreach name=workorders item=record from=$WorkOrders}
	{cycle values="odd,even" assign="rowClass"}
	<tr class="{$rowClass}">
	{foreach name=wo item=data key=key from=$record}
	{if is_numeric($key)}
	<td>{if $smarty.foreach.wo.iteration < 3}<a href="{$URL_MAIN_PHP}?menuAction=boWorkorders.viewjcn&jcn={$record.jcn}&seq={$record.seq}">{$data}</a>{else}{$data}{/if}</td>
	{/if}
	{/foreach}
	</tr>
{/foreach}
	</tbody>
</table>