<script language="JavaScript">

	$(document).ready(function() {
		var urlMainPhp = "{$URL_MAIN_PHP}";
		var contactId = {Contact->contact_id};
		$(".dcl-delete-contact-address").click(function() {
			if (confirm("Are you sure you want to delete this address?")) {
				var id = $(this).attr("data-id");
				var $row = $(this).parents("tr:first");
				$.ajax({
					type: "POST",
					url: urlMainPhp,
					data: { menuAction: "ContactAddress.Destroy", contact_id: contactId, contact_addr_id: id },
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

		$(".dcl-delete-contact-email").click(function() {
			if (confirm("Are you sure you want to delete this email?")) {
				var id = $(this).attr("data-id");
				var $row = $(this).parents("tr:first");
				$.ajax({
					type: "POST",
					url: urlMainPhp,
					data: { menuAction: "ContactEmail.Destroy", contact_id: contactId, contact_email_id: id },
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

		$(".dcl-delete-contact-license").click(function() {
			if (confirm("Are you sure you want to delete this license?")) {
				var id = $(this).attr("data-id");
				var $row = $(this).parents("tr:first");
				var $notesRow = $row.next().find("td.notes").parents("tr:first");
				$.ajax({
					type: "POST",
					url: urlMainPhp,
					data: { menuAction: "ContactLicense.Destroy", contact_id: contactId, contact_license_id: id },
					success: function() {
						$row.remove();
						$notesRow.remove();
						$.gritter.add({
							title: "Success",
							text: "License deleted successfully."
						});
					},
					error: function() {
						$.gritter.add({ title: "Error", text: "License was not deleted successfully." });
					}
				});
			}
		});

		$(".dcl-delete-contact-phone").click(function() {
			if (confirm("Are you sure you want to delete this phone number?")) {
				var id = $(this).attr("data-id");
				var $row = $(this).parents("tr:first");
				$.ajax({
					type: "POST",
					url: urlMainPhp,
					data: { menuAction: "ContactPhone.Destroy", contact_id: contactId, contact_phone_id: id },
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

		$(".dcl-delete-contact-url").click(function() {
			if (confirm("Are you sure you want to delete this URL?")) {
				var id = $(this).attr("data-id");
				var $row = $(this).parents("tr:first");
				$.ajax({
					type: "POST",
					url: urlMainPhp,
					data: { menuAction: "ContactUrl.Destroy", contact_id: contactId, contact_url_id: id },
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
<h4>Contact [{Contact->contact_id}] {Contact->first_name} {Contact->last_name}</h4>
<table width="100%" class="table table-striped">
	<thead>
		<tr>
			<th>
				<div class="btn-group">
					<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlContactBrowse.show&filterActive=Y">Browse</a>
					<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlContact.viewWorkOrders&id={Contact->contact_id}">Work Orders</a>
					<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlContact.viewTickets&id={Contact->contact_id}">Tickets</a>
					{if $PERM_MODIFY}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlContact.merge&contact_id={Contact->contact_id}">Merge</a>{/if}
					{if $PERM_MODIFY}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlContactForm.modify&contact_id={Contact->contact_id}">{$smarty.const.STR_CMMN_EDIT}</a>{/if}
					{if $PERM_DELETE}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlContactForm.delete&contact_id={Contact->contact_id}">{$smarty.const.STR_CMMN_DELETE}</a>{/if}
				</div>
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
<h4>{$smarty.const.STR_CM_ADDR}Addresses</h4>
<table width="100%" class="table table-striped">
	<thead>
		<tr>
			<th colspan="3">
				<div class="btn-group"><a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=ContactAddress.Create&contact_id={Contact->contact_id}">{$smarty.const.STR_CMMN_NEW}</a>
			</th>
		</tr>
	</thead>
	<tbody>
		{section name=address loop=$ContactAddress}
		<tr>
		<td class="rowheader">{$ContactAddress[address].addr_type_name}{if $ContactAddress[address].preferred == "Y"} <span class="glyphicon glyphicon-flag"></span>{/if}</td>
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
<h4>{$smarty.const.STR_CM_PHONENUMBERS}Phone Numbers</h4>
<table width="100%" class="table table-striped">
	<thead>
		<tr>
			<th colspan="3">
				<div class="btn-group"><a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=ContactPhone.Create&contact_id={Contact->contact_id}">{$smarty.const.STR_CMMN_NEW}</a></div>
			</th>
		</tr>
	</thead>
	<tbody>
{section name=phone loop=$ContactPhone}
		<tr">
		<td class="rowheader">{$ContactPhone[phone].phone_type_name}{if $ContactPhone[phone].preferred == "Y"} <span class="glyphicon glyphicon-flag"></span>{/if}</td>
		<td>{$ContactPhone[phone].phone_number}</td>
		<td class="options">
{strip}
		{if $PERM_MODIFY}
			<a href="{$URL_MAIN_PHP}?menuAction=ContactPhone.Edit&contact_id={Contact->contact_id}&contact_phone_id={$ContactPhone[phone].contact_phone_id}">{$smarty.const.STR_CMMN_EDIT}</a>
			&nbsp;|&nbsp;
			<a href="javascript:;" class="dcl-delete-contact-phone" data-id="{$ContactPhone[phone].contact_phone_id}">{$smarty.const.STR_CMMN_DELETE}</a>
		{/if}
{/strip}
		</td>
	</tr>
{/section}
	</tbody>
</table>
<h4>{$smarty.const.STR_CM_EMAILADDRESSES}E-Mail Addresses</h4>
<table width="100%" class="table table-striped">
	<thead>
		<tr>
			<th colspan="3">
				<div class="btn-group"><a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=ContactEmail.Create&contact_id={Contact->contact_id}">{$smarty.const.STR_CMMN_NEW}</a></div>
			</th>
		</tr>
	</thead>
	<tbody>
{section name=email loop=$ContactEmail}
	<tr>
		<td class="rowheader">{$ContactEmail[email].email_type_name}{if $ContactEmail[email].preferred == "Y"} <span class="glyphicon glyphicon-flag"></span>{/if}</td>
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
<h4>{$smarty.const.STR_CM_URL}URLs</h4>
<table width="100%" class="table table-striped">
	<thead>
		<tr>
			<th colspan="3">
				<div class="btn-group"><a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=ContactUrl.Create&contact_id={Contact->contact_id}">{$smarty.const.STR_CMMN_NEW}</a></div>
			</th>
		</tr>
	</thead>
	<tbody>
{section name=url loop=$ContactURL}
	<tr>
		<td class="rowheader">{$ContactURL[url].url_type_name}{if $ContactURL[url].preferred == "Y"} <span class="glyphicon glyphicon-flag"></span>{/if}</td>
		<td><a target="_blank" href="{$ContactURL[url].url_addr|escape}">{$ContactURL[url].url_addr|escape}</a></td>
		<td class="options">
{strip}
		{if $PERM_MODIFY}
			<a href="{$URL_MAIN_PHP}?menuAction=ContactUrl.Edit&contact_id={Contact->contact_id}&contact_url_id={$ContactURL[url].contact_url_id}">{$smarty.const.STR_CMMN_EDIT}</a>
			&nbsp;|&nbsp;
			<a href="javascript:;" class="dcl-delete-contact-url" data-id="{$ContactURL[url].contact_url_id}">{$smarty.const.STR_CMMN_DELETE}</a>
		{/if}
{/strip}
		</td>
	</tr>
{/section}
	</tbody>
</table>
<h4>{$smarty.const.STR_CM_ORGANIZATIONS}Organizations</h4>
<table width="100%" class="table table-striped">
	<thead>
		{if $PERM_MODIFY}<tr><th colspan="5"><div class="btn-group"><a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlContactOrgs.modify&contact_id={Contact->contact_id}">{$smarty.const.STR_CMMN_EDIT}</a></div></th></tr>{/if}
		<tr>{foreach item=col from=$ViewOrg->columnhdrs}<th>{$col}</th>{/foreach}</tr>
	</thead>
	<tbody>
{foreach name=orgs item=record from=$Orgs}
{strip}
	<tr>
	{foreach name=org key=key item=data from=$record}
	{if is_numeric($key)}<td>
		{if $smarty.foreach.org.iteration == 2}<a href="{$URL_MAIN_PHP}?menuAction=Organization.Detail&org_id={$record.org_id}">{$data}</a>
		{elseif $smarty.foreach.org.iteration == 4 && $data != ""}{mailto address=$data}
		{elseif $smarty.foreach.org.iteration == 5}{$data|escape|dcl_link}
		{else}{$data}
		{/if}</td>
	{/if}
	{/foreach}
	</tr>
{/strip}
{/foreach}
	</tbody>	
</table>
<h4>{$smarty.const.STR_CM_PRODUCTLICENSES}Product Licenses</h4>
<table width="100%" class="table table-striped">
	<thead>
		{if $PERM_MODIFY}<tr><th colspan="{if $PERM_MODIFY}6{else}5{/if}"><div class="btn-group"><a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=ContactLicense.Create&contact_id={Contact->contact_id}">{$smarty.const.STR_CMMN_NEW}</a></div></th></tr>{/if}
		<tr><th>Product</th><th>Version</th><th>License #</th><th>Registered On</th><th>Expires On</th>{if $PERM_MODIFY}<th>Options</th>{/if}</tr>
	</thead>
	<tbody>
{section name=license loop=$ContactLicense}
	<tr>
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
<h4>{$smarty.const.STR_CM_LAST10TICKETS}Last 10 Tickets</h4>
<table width="100%" class="table table-striped">
	<thead><tr><th colspan="7"><div class="btn-group"><a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=boTickets.add&contact_id={Contact->contact_id}">{$smarty.const.STR_CMMN_NEW}</a></div></th></tr>
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
<h4>{$smarty.const.STR_CM_LAST10WORKORDERS}Last 10 Work Orders</h4>
<table width="100%" class="table table-striped">
	<thead><tr><th colspan="9"><div class="btn-group"><a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=WorkOrder.CreateForContact&contact_id={Contact->contact_id}">{$smarty.const.STR_CMMN_NEW}</a></div></th></tr>
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