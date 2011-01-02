<!-- $Id$ -->
<script language="JavaScript">
{literal}
	$(document).ready(function() {
		var urlMainPhp = {/literal}"{$URL_MAIN_PHP}"{literal};
		var contactId = {/literal}{Contact->contact_id}{literal};
		$(".dcl-delete-contact-address").click(function() {
			if (confirm("Are you sure you want to delete this address?")) {
				var id = $(this).attr("data-id");
				var $row = $(this).parents("tr:first");
				$.ajax({
					type: "POST",
					url: urlMainPhp,
					data: {{/literal}menuAction: "ContactAddress.Destroy", contact_id: contactId, contact_addr_id: id{literal}},
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

		$(".dcl-delete-contact-email").click(function() {
			if (confirm("Are you sure you want to delete this email?")) {
				var id = $(this).attr("data-id");
				var $row = $(this).parents("tr:first");
				$.ajax({
					type: "POST",
					url: urlMainPhp,
					data: {{/literal}menuAction: "ContactEmail.Destroy", contact_id: contactId, contact_email_id: id{literal}},
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

		$(".dcl-delete-contact-license").click(function() {
			if (confirm("Are you sure you want to delete this license?")) {
				var id = $(this).attr("data-id");
				var $row = $(this).parents("tr:first");
				var $notesRow = $row.next().find("td.notes").parents("tr:first");
				$.ajax({
					type: "POST",
					url: urlMainPhp,
					data: {{/literal}menuAction: "ContactLicense.Destroy", contact_id: contactId, contact_license_id: id{literal}},
					success: function() {
						$row.remove();
						$notesRow.remove();
						$.gritter.add({
							title: "Success",
							text: "License deleted successfully."
						});
					},
					error: function() {
						$.gritter.add({title: "Error", text: "License was not deleted successfully."});
					}
				});
			}
		});
	});
	function deletePhone(id) {
		if (confirm("Are you sure you want to delete this phone number?"))
{/literal}
			location.href = "{$URL_MAIN_PHP}?menuAction=htmlContactPhone.submitDelete&contact_id={Contact->contact_id}&contact_phone_id=" + id;
{literal}
	}
	function deleteUrl(id) {
		if (confirm("Are you sure you want to delete this URL?"))
{/literal}
			location.href = "{$URL_MAIN_PHP}?menuAction=htmlContactUrl.submitDelete&contact_id={Contact->contact_id}&contact_url_id=" + id;
{literal}
	}
{/literal}
</script>
<table width="100%" class="dcl_results">
	<caption>Contact [{Contact->contact_id}] {Contact->first_name} {Contact->last_name}</caption>
	<thead>
		<tr class="toolbar">
			<th>
				<ul>
					<li class="first"><a href="{$URL_MAIN_PHP}?menuAction=htmlContactBrowse.show&filterActive=Y">Browse</a></li>
					<li><a href="{$URL_MAIN_PHP}?menuAction=htmlContact.viewWorkOrders&id={Contact->contact_id}">Work Orders</a></li>
					<li><a href="{$URL_MAIN_PHP}?menuAction=htmlContact.viewTickets&id={Contact->contact_id}">Tickets</a></li>
					{if $PERM_MODIFY}<li><a href="{$URL_MAIN_PHP}?menuAction=htmlContact.merge&contact_id={Contact->contact_id}">Merge</a></li>{/if}
					{if $PERM_MODIFY}<li><a href="{$URL_MAIN_PHP}?menuAction=htmlContactForm.modify&contact_id={Contact->contact_id}">{$smarty.const.STR_CMMN_EDIT}</a></li>{/if}
					{if $PERM_DELETE}<li><a href="{$URL_MAIN_PHP}?menuAction=htmlContactForm.delete&contact_id={Contact->contact_id}">{$smarty.const.STR_CMMN_DELETE}</a></li>{/if}
				</ul>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr><td class="string">
{section name=typeitem loop=$ContactType}
{$ContactType[typeitem].contact_type_name}{if !$smarty.section.typeitem.last},&nbsp;{/if}
{sectionelse}
No contact types!
{/section}
		</td></tr>
	</tbody>
</table>
<table width="100%" class="dcl_results">
	<caption class="spacer">{$smarty.const.STR_CM_ADDR}Addresses</caption>
	<thead>
		<tr class="toolbar"><th colspan="3"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=ContactAddress.Create&contact_id={Contact->contact_id}" class="adark">{$smarty.const.STR_CMMN_NEW}</a></li></ul></th></tr>
	</thead>
	<tbody>
		{section name=address loop=$ContactAddress}
		{cycle values="odd,even" assign="rowClass"}
		<tr class="{$rowClass}">
		<td class="rowheader">{$ContactAddress[address].addr_type_name}{if $ContactAddress[address].preferred == "Y"}<span style="color:#a00000;">*</span>{/if}</td>
		<td>
		{$ContactAddress[address].add1}<br />
		{if $ContactAddress[address].add2 != ""}
			{$ContactAddress[address].add2}<br />
		{/if}
		{$ContactAddress[address].city}, {$ContactAddress[address].state}   {$ContactAddress[address].zip}
		{if $ContactAddress[address].country != ""}
			<br />{$ContactAddress[address].country}
		{/if}
		</td>
{strip}
		<td class="options">
		{if $PERM_MODIFY}
			<a href="{$URL_MAIN_PHP}?menuAction=ContactAddress.Edit&contact_id={Contact->contact_id}&contact_addr_id={$ContactAddress[address].contact_addr_id}">{$smarty.const.STR_CMMN_EDIT}</a>
			&nbsp;|&nbsp;
			<a href="javascript:;" class="dcl-delete-contact-address" data-id="{$ContactAddress[address].contact_addr_id}">{$smarty.const.STR_CMMN_DELETE}</a>
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
		<tr class="toolbar"><th colspan="3"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=htmlContactPhone.add&contact_id={Contact->contact_id}" class="adark">{$smarty.const.STR_CMMN_NEW}</a></li></ul></th></tr>
	</thead>
	<tbody>
{section name=phone loop=$ContactPhone}
		{cycle values="odd,even" assign="rowClass"}
		<tr class="{$rowClass}">
		<td class="rowheader">{$ContactPhone[phone].phone_type_name}{if $ContactPhone[phone].preferred == "Y"}<span style="color:#a00000;">*</span>{/if}</td>
		<td>{$ContactPhone[phone].phone_number}</td>
		<td class="options">
{strip}
		{if $PERM_MODIFY}
			<a class="adark" href="{$URL_MAIN_PHP}?menuAction=htmlContactPhone.modify&contact_id={Contact->contact_id}&contact_phone_id={$ContactPhone[phone].contact_phone_id}">{$smarty.const.STR_CMMN_EDIT}</a>
			&nbsp;|&nbsp;
			<a class="adark" href="javascript:;" onclick="deletePhone({$ContactPhone[phone].contact_phone_id});">{$smarty.const.STR_CMMN_DELETE}</a>
		{/if}
{/strip}
		</td>
	</tr>
{/section}
	</tbody>
</table>
<table width="100%" class="dcl_results">
	<caption class="spacer">{$smarty.const.STR_CM_EMAILADDRESSES}E-Mail Addresses</caption>
	<thead>
		<tr class="toolbar"><th colspan="3"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=ContactEmail.Create&contact_id={Contact->contact_id}">{$smarty.const.STR_CMMN_NEW}</a></li></ul></th></tr>
	</thead>
	<tbody>
{section name=email loop=$ContactEmail}
		{cycle values="odd,even" assign="rowClass"}
	<tr class="{$rowClass}">
		<td class="rowheader">{$ContactEmail[email].email_type_name}{if $ContactEmail[email].preferred == "Y"}<span style="color:#a00000;">*</span>{/if}</td>
		<td><a href="mailto:{$ContactEmail[email].email_addr|escape}">{$ContactEmail[email].email_addr|escape}</a></td>
		<td class="options">
{strip}
		{if $PERM_MODIFY}
			<a href="{$URL_MAIN_PHP}?menuAction=ContactEmail.Edit&contact_id={Contact->contact_id}&contact_email_id={$ContactEmail[email].contact_email_id}">{$smarty.const.STR_CMMN_EDIT}</a>
			&nbsp;|&nbsp;
			<a href="javascript:;" class="dcl-delete-contact-email" data-id="{$ContactEmail[email].contact_email_id}">{$smarty.const.STR_CMMN_DELETE}</a>
		{/if}
{/strip}
		</td>
	</tr>
{/section}
	</tbody>
</table>
<table width="100%" class="dcl_results">
	<caption class="spacer">{$smarty.const.STR_CM_URL}URLs</caption>
	<thead>
		<tr class="toolbar"><th colspan="3"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=htmlContactUrl.add&contact_id={Contact->contact_id}">{$smarty.const.STR_CMMN_NEW}</a></li></ul></th></tr>
	</thead>
	<tbody>
{section name=url loop=$ContactURL}
		{cycle values="odd,even" assign="rowClass"}
	<tr class="{$rowClass}">
		<td class="rowheader">{$ContactURL[url].url_type_name}{if $ContactURL[url].preferred == "Y"}<span style="color:#a00000;">*</span>{/if}</td>
		<td><a target="_blank" href="{$ContactURL[url].url_addr|escape}">{$ContactURL[url].url_addr|escape}</a></td>
		<td class="options">
{strip}
		{if $PERM_MODIFY}
			<a href="{$URL_MAIN_PHP}?menuAction=htmlContactUrl.modify&contact_id={Contact->contact_id}&contact_url_id={$ContactURL[url].contact_url_id}">{$smarty.const.STR_CMMN_EDIT}</a>
			&nbsp;|&nbsp;
			<a href="javascript:;" onclick="deleteUrl({$ContactURL[url].contact_url_id});">{$smarty.const.STR_CMMN_DELETE}</a>
		{/if}
{/strip}
		</td>
	</tr>
{/section}
	</tbody>
</table>
<table width="100%" class="dcl_results">
	<caption class="spacer">{$smarty.const.STR_CM_ORGANIZATIONS}Organizations</caption>
	<thead>
		{if $PERM_MODIFY}<tr class="toolbar"><th colspan="5"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=htmlContactOrgs.modify&contact_id={Contact->contact_id}">{$smarty.const.STR_CMMN_EDIT}</a></li></ul></th></tr>{/if}
		<tr>{foreach item=col from=$ViewOrg->columnhdrs}<th>{$col}</th>{/foreach}</tr>
	</thead>
	<tbody>
{foreach name=orgs item=record from=$Orgs}
{strip}
	{cycle values="odd,even" assign="rowClass"}
	<tr class="{$rowClass}">
	{foreach name=org key=key item=data from=$record}
	{if is_numeric($key)}<td>
		{if $smarty.foreach.org.iteration == 2}<a href="{$URL_MAIN_PHP}?menuAction=htmlOrgDetail.show&org_id={$record.org_id}">{$data}</a>
		{elseif $smarty.foreach.org.iteration == 4 && $data != ""}{mailto address=$data}
		{elseif $smarty.foreach.org.iteration == 5}{$data|escape:"link"}
		{else}{$data}
		{/if}</td>
	{/if}
	{/foreach}
	</tr>
{/strip}
{/foreach}
	</tbody>	
</table>
<table width="100%" class="dcl_results">
	<caption class="spacer">{$smarty.const.STR_CM_PRODUCTLICENSES}Product Licenses</caption>
	<thead>
		{if $PERM_MODIFY}<tr class="toolbar"><th colspan="{if $PERM_MODIFY}6{else}5{/if}"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=ContactLicense.Create&contact_id={Contact->contact_id}">{$smarty.const.STR_CMMN_NEW}</a></li></ul></th></tr>{/if}
		<tr><th>Product</th><th>Version</th><th>License #</th><th>Registered On</th><th>Expires On</th>{if $PERM_MODIFY}<th>Options</th>{/if}</tr>
	</thead>
	<tbody>
{section name=license loop=$ContactLicense}
		{cycle values="odd,even" assign="rowClass"}
	<tr class="{$rowClass}">
		<td>{$ContactLicense[license].name|escape}</td>
		<td>{$ContactLicense[license].product_version|escape}</td>
		<td>{$ContactLicense[license].license_id|escape}</td>
		<td>{$ContactLicense[license].registered_on|escape:"date"}</td>
		<td class="{if $ContactLicense[license].val_expires_on >= $VAL_TODAY}no{/if}problem">{$ContactLicense[license].expires_on|escape:"date"}</td>
{strip}
		{if $PERM_MODIFY}
		<td class="options">
			<a href="{$URL_MAIN_PHP}?menuAction=ContactLicense.Edit&contact_id={Contact->contact_id}&contact_license_id={$ContactLicense[license].contact_license_id}">{$smarty.const.STR_CMMN_EDIT}</a>
			&nbsp;|&nbsp;
			<a href="javascript:;" class="dcl-delete-contact-license" data-id="{$ContactLicense[license].contact_license_id}">{$smarty.const.STR_CMMN_DELETE}</a>
		</td>
		{/if}
{/strip}
	</tr>
	{if $ContactLicense[license].license_notes != ''}
	<tr>
		<td>&nbsp;</td>
		<td class="notes" colspan="{if $PERM_MODIFY}5{else}4{/if}"><b>Notes: </b>{$ContactLicense[license].license_notes|escape}</td>
	</tr>
	{/if}
{/section}
	</tbody>
</table>
<table width="100%" class="dcl_results">
	<caption class="spacer">{$smarty.const.STR_CM_LAST10TICKETS}Last 10 Tickets</caption>
	<thead><tr class="toolbar"><th colspan="7"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=boTickets.add&contact_id={Contact->contact_id}">{$smarty.const.STR_CMMN_NEW}</a></li></ul></th></tr>
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
	<thead><tr class="toolbar"><th colspan="9"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=boWorkorders.newjcn&contact_id={Contact->contact_id}">{$smarty.const.STR_CMMN_NEW}</a></li></ul></th></tr>
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