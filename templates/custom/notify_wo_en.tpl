You are receiving this e-mail because you are (1) directly involved, or
(2) have a watch on this work order, or (3) have a watch on an account,
product or project associated with this work order.  This is a snapshot
of the work order.

You can see the detail at: {dcl_config name=DCL_ROOT}?menuAction=boWorkorders.viewjcn&jcn={$obj->jcn}&seq={$obj->seq}

[{$obj->jcn}-{$obj->seq}] {$obj->summary}

{$smarty.const.STR_WO_RESPONSIBLE}: {dcl_metadata_display type='personnel' value="`$obj->responsible`"}
{$smarty.const.STR_WO_TYPE}: {dcl_metadata_display type='wotype' value="`$obj->wo_type_id`"}
{$smarty.const.STR_WO_PRIORITY}: {dcl_metadata_display type='priority' value="`$obj->priority`"}
{$smarty.const.STR_WO_SEVERITY}: {dcl_metadata_display type='severity' value="`$obj->severity`"}
{$smarty.const.STR_WO_DEADLINE}: {$obj->deadlineon}
{$smarty.const.STR_WO_PRODUCT}: {dcl_metadata_display type='product' value="`$obj->product`"}
{$smarty.const.STR_CMMN_MODULE}: {dcl_metadata_display type='module' value="`$obj->module`"}
{$smarty.const.STR_WO_REVISION}: {$obj->revision}
{$smarty.const.STR_WO_ESTSTART}: {$obj->eststarton}
{$smarty.const.STR_WO_START}: {$obj->starton}
{$smarty.const.STR_WO_ESTEND}: {$obj->estendon}
{$smarty.const.STR_WO_END}: {$obj->endon}
{$smarty.const.STR_WO_ESTHOURS}: {$obj->esthours}
{$smarty.const.STR_WO_ACTHOURS}: {$obj->totalhours}
{$smarty.const.STR_WO_ETCHOURS}: {$obj->etchours}
{$smarty.const.STR_WO_OPENBY}: {dcl_metadata_display type='personnel' value="`$obj->createby`"} on {$obj->createdon}
{$smarty.const.STR_WO_CLOSEBY}: {dcl_metadata_display type='personnel' value="`$obj->closedby`"} on {$obj->closedon}
{$smarty.const.STR_WO_STATUS}: {dcl_metadata_display type='status' value="`$obj->status`"} on {$obj->statuson}
{$smarty.const.STR_WO_LASTACTION}: {$obj->lastactionon}
{$smarty.const.STR_WO_ACCOUNT}: {dcl_metadata_display type='wo_org' value="`$obj->jcn`" value2="`$obj->seq`"}
{$smarty.const.STR_WO_CONTACT}: {dcl_metadata_display type='contact_name' value="`$obj->contact_id`"}
{$smarty.const.STR_WO_CONTACTPHONE}: {dcl_metadata_display type='contact_phone' value="`$obj->contact_id`"}

{$smarty.const.STR_WO_NOTES}: {$obj->notes}

{$smarty.const.STR_WO_DESCRIPTION}: {$obj->description}
{section name=tc loop=$VAL_TIMECARDS}
--------------------------------------------------
{$VAL_TIMECARDS[tc].actionby} ({$VAL_TIMECARDS[tc].actionon}) - {$VAL_TIMECARDS[tc].summary}
{$smarty.const.STR_TC_STATUS}: {$VAL_TIMECARDS[tc].status}
{$smarty.const.STR_TC_VERSION}: {$VAL_TIMECARDS[tc].version}
{$smarty.const.STR_TC_ACTION}: {$VAL_TIMECARDS[tc].action}
{$smarty.const.STR_TC_HOURS}: {$VAL_TIMECARDS[tc].hours}
{$smarty.const.STR_CMMN_REASSIGN}: {$VAL_TIMECARDS[tc].reassign_from_id} {$smarty.const.STR_CMMN_TO}: {$VAL_TIMECARDS[tc].reassign_to_id}
{if $VAL_TIMECARDS[tc].description != ""}{$smarty.const.STR_TC_DESCRIPTION}: {$VAL_TIMECARDS[tc].description}{/if}
{/section}