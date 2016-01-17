You are receiving this e-mail because you are (1) directly involved, or
(2) have a watch on this ticket, or (3) have a watch on an account or
product associated with this ticket.  This is a snapshot of the ticket.

See The Detail At: {dcl_config name=DCL_ROOT}main.php?menuAction=boTickets.view&ticketid={$obj->ticketid}

[{$obj->ticketid}] {$obj->summary}

{$smarty.const.STR_TCK_RESPONSIBLE}: {dcl_metadata_display type='personnel' value=$obj->responsible}
{$smarty.const.STR_TCK_STATUS}: {dcl_metadata_display type='status' value=$obj->status} on {$obj->statuson}
{$smarty.const.STR_TCK_OPENEDBY}: {dcl_metadata_display type='personnel' value=$obj->createdby} on {$obj->createdon}
{$smarty.const.STR_TCK_CLOSEDBY}: {dcl_metadata_display type='personnel' value=$obj->closedby} on {$obj->closedon}
{$smarty.const.STR_TCK_LASTACTIONON}: {$obj->lastactionon}
{$smarty.const.STR_TCK_PRIORITY}: {dcl_metadata_display type='priority' value=$obj->priority}
{$smarty.const.STR_TCK_TYPE}: {dcl_metadata_display type='severity' value=$obj->type}
{$smarty.const.STR_TCK_PRODUCT}: {dcl_metadata_display type='product' value=$obj->product}
{$smarty.const.STR_TCK_MODULE}: {dcl_metadata_display type='module' value=$obj->module}
{$smarty.const.STR_TCK_VERSION}: {$obj->version}
{$smarty.const.STR_TCK_ACCOUNT}: {dcl_metadata_display type='org_name' value=$obj->account}
{$smarty.const.STR_TCK_CONTACT}: {dcl_metadata_display type='contact_name' value=$obj->contact_id}
{$smarty.const.STR_TCK_CONTACTPHONE}: {dcl_metadata_display type='contact_phone' value=$obj->contact_id}
{$smarty.const.STR_TCK_ISSUE}: {$obj->issue}
{section name=tr loop=$VAL_RESOLUTIONS}
--------------------------------------------------
Logged By {$VAL_RESOLUTIONS[tr].loggedby} On {$VAL_RESOLUTIONS[tr].loggedon}

{$smarty.const.STR_TCK_STATUS}: {$VAL_RESOLUTIONS[tr].status}
{$smarty.const.STR_TCK_RESOLUTION}: {$VAL_RESOLUTIONS[tr].resolution}
{/section}
