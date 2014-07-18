<h4>Audit: [{$VAL_ID}] {$VAL_SUMMARY|escape:"htmlall"}</h4>
<table class="table table-striped">
	<thead>
		<tr class="toolbar"><th colspan="5"><a href="{$LNK_BACK}">Back</a></th></tr>
		<tr><th>By</th><th>On</th><th>Field</th><th>Old Value</th><th>New Value</th></tr>
	</thead>
	<tbody>
{section name=version loop=$VAL_AUDITTRAIL}
{strip}
		<tr><td colspan="5">Changes for Version: {$VAL_AUDITTRAIL[version].audit_version}</td></tr>
		<tr>
			<td>{$VAL_AUDITTRAIL[version].audit_by|escape:"htmlall"}</td>
			<td>{$VAL_AUDITTRAIL[version].audit_on}</td>
			{section name=change loop=$VAL_AUDITTRAIL[version].changes}
				{if !$smarty.section.change.first}
					</tr><tr><td></td><td></td>
				{/if}
				<td>{$VAL_AUDITTRAIL[version].changes[change].field|escape:"htmlall"}</td>
				<td>{$VAL_AUDITTRAIL[version].changes[change].old|escape:"htmlall"}</td>
				<td>{$VAL_AUDITTRAIL[version].changes[change].new|escape:"htmlall"}</td>
			{/section}
		</tr>
{/strip}
{/section}
	</tbody>
</table>
{if (count($VAL_AUDITPROJECT) > 0)}
<h4>Project Audit</h4>
<table class="table table-striped">
	<thead>
		<tr class="toolbar"><th colspan="4"><a href="{$LNK_BACK}">Back</a></th></tr>
		<tr><th>By</th><th>On</th><th>Action</th><th>Project</th></tr>
	</thead>
	<tbody>
{section name=project loop=$VAL_AUDITPROJECT}
{strip}
		<tr>
			<td>{$VAL_AUDITPROJECT[project].audit_by|escape:"htmlall"}</td>
			<td>{$VAL_AUDITPROJECT[project].audit_on}</td>
			<td>{$VAL_AUDITPROJECT[project].audit_type|escape:"htmlall"}</td>
			<td><a href="{$URL_MAIN_PHP}?menuAction=Project.Detail&id={$VAL_AUDITPROJECT[project].projectid}&wostatus=0">[{$VAL_AUDITPROJECT[project].projectid}] {$VAL_AUDITPROJECT[project].name|escape:"htmlall"}</a></td>
		</tr>
{/strip}
{/section}
	</tbody>
</table>
{/if}
{if (count($VAL_AUDITACCOUNT) > 0)}
<h4>Account Audit</h4>
<table class="table table-striped">
	<thead>
		<tr class="toolbar"><th colspan="4"><a href="{$LNK_BACK}">Back</a></th></tr>
		<tr><th>By</th><th>On</th><th>Action</th><th>Account</th></tr>
	</thead>
	<tbody>
{section name=account loop=$VAL_AUDITACCOUNT}
{strip}
		<tr>
			<td>{$VAL_AUDITACCOUNT[account].audit_by|escape:"htmlall"}</td>
			<td>{$VAL_AUDITACCOUNT[account].audit_on}</td>
			<td>{$VAL_AUDITACCOUNT[account].audit_type|escape:"htmlall"}</td>
			<td><a href="{$URL_MAIN_PHP}?menuAction=Organization.Detail&org_id={$VAL_AUDITACCOUNT[account].account_id}">{$VAL_AUDITACCOUNT[account].name|escape:"htmlall"}</a></td>
		</tr>
{/strip}
{/section}
	</tbody>
</table>
{/if}
{if (count($VAL_AUDITWORKORDER) > 0)}
<h4>Task Audit</h4>
<table class="table table-striped">
	<thead>
		<tr class="toolbar"><th colspan="4"><a href="{$LNK_BACK}">Back</a></th></tr>
		<tr><th>By</th><th>On</th><th>Action</th><th>Task</th></tr>
	</thead>
	<tbody>
{section name=wo loop=$VAL_AUDITWORKORDER}
{strip}
		<tr>
			<td>{$VAL_AUDITWORKORDER[wo].audit_by|escape:"htmlall"}</td>
			<td>{$VAL_AUDITWORKORDER[wo].audit_on}</td>
			<td>{$VAL_AUDITWORKORDER[wo].audit_type|escape:"htmlall"}</td>
			<td><a href="{$URL_MAIN_PHP}?menuAction=WorkOrder.Detail&jcn={$VAL_AUDITWORKORDER[wo].jcn}&seq={$VAL_AUDITWORKORDER[wo].seq}">[{$VAL_AUDITWORKORDER[wo].jcn}-{$VAL_AUDITWORKORDER[wo].seq}] {$VAL_AUDITWORKORDER[wo].summary|escape:"htmlall"}</a></td>
		</tr>
{/strip}
{/section}
	</tbody>
</table>
{/if}