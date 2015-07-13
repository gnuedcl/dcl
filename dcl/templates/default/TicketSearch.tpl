<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
<script type="text/javascript" src="{$DIR_JS}ticketsearch.js"></script>
<form class="form-horizontal" name="mondosearchform" action="{$URL_MAIN_PHP}" method="post">
	<input type="hidden" name="menuAction" value="boTickets.dbsearch">
	<fieldset>
		<legend>{$smarty.const.STR_TCK_SEARCHTITLE}</legend>
	{if !$IS_PUBLIC}
		{dcl_form_control controlsize=10 label="Personnel Fields"}
			<label><input type="checkbox" id="responsible" name="responsible" value="1"{$CHK_RESPONSIBLE}> {$smarty.const.STR_TCK_RESPONSIBLE|escape}</label>
			<label><input type="checkbox" id="createdby" name="createdby" value="1"{$CHK_CREATEDBY}> {$smarty.const.STR_TCK_OPENEDBY|escape}</label>
			<label><input type="checkbox" id="closedby" name="closedby" value="1"{$CHK_CLOSEDBY}> {$smarty.const.STR_TCK_CLOSEDBY|escape}</label>
		{/dcl_form_control}
		{dcl_form_control id=department controlsize=10 label=Department}
		{dcl_select_department name=department default=$VAL_DEPARTMENTS size=8}
		{/dcl_form_control}
		{dcl_form_control id=personnel controlsize=10 label=Personnel}
		{dcl_select_personnel name=personnel default=$VAL_PERSONNEL size=8}
		{/dcl_form_control}
	{/if}
		{dcl_form_control id=product controlsize=10 label=$smarty.const.STR_TCK_PRODUCT}
		{dcl_select_product default=$VAL_PRODUCT size=8 active=N}
		{/dcl_form_control}
		{dcl_form_control id=module_id controlsize=10 label=$smarty.const.STR_CMMN_MODULE}
		{dcl_select_module default=$VAL_MODULE size=8 active=N}
		{/dcl_form_control}
		{dcl_form_control id=account controlsize=10 label=$smarty.const.STR_TCK_ACCOUNT}
		{$CMB_ACCOUNTS}
		{/dcl_form_control}
		{dcl_form_control id=priority controlsize=10 label=$smarty.const.STR_TCK_PRIORITY}
		{dcl_select_priority default=$VAL_PRIORITY size=8}
		{/dcl_form_control}
		{dcl_form_control id=type controlsize=10 label=$smarty.const.STR_TCK_TYPE}
		{dcl_select_severity default=$VAL_SEVERITY size=8 name=type}
		{/dcl_form_control}
		{dcl_form_control id=dcl_status_type controlsize=10 label=$smarty.const.STR_CMMN_STATUSTYPE}
		{$CMB_STATUSTYPES}
		{/dcl_form_control}
		{dcl_form_control id=status controlsize=10 label=$smarty.const.STR_TCK_STATUS}
		{$CMB_STATUSESEMPTY}
		{/dcl_form_control}
		{if !$IS_PUBLIC}
			{dcl_form_control id=is_public controlsize=10 label=$smarty.const.STR_CMMN_PUBLIC}
			{$CMB_PUBLIC}
			{/dcl_form_control}
		{/if}
		{dcl_form_control id=entity_source_id controlsize=10 label=$smarty.const.STR_CMMN_SOURCE}
		{dcl_select_source default=$VAL_SOURCE size=8}
		{/dcl_form_control}
		{dcl_form_control controlsize=10 label="Date Fields"}
			<label><input type="checkbox" id="createdon" name="createdon" value="1"{$CHK_CREATEDON}> {$smarty.const.STR_TCK_OPENEDON}</label>
			<label><input type="checkbox" id="closedon" name="closedon" value="1"{$CHK_CLOSEDON}> {$smarty.const.STR_TCK_CLOSEDON}</label>
			<label><input type="checkbox" id="statuson" name="statuson" value="1"{$CHK_STATUSON}> {$smarty.const.STR_TCK_STATUSON}</label>
			<label><input type="checkbox" id="lastactionon" name="lastactionon" value="1"{$CHK_LASTACTIONON}> {$smarty.const.STR_TCK_LASTACTIONON}</label>
		{/dcl_form_control}
		{dcl_form_control id=dateFrom controlsize=2 label=$smarty.const.STR_CMMN_FROM}
		{dcl_input_date id=dateFrom value=$VAL_DATEFROM}
		{/dcl_form_control}
		{dcl_form_control id=dateTo controlsize=2 label=$smarty.const.STR_CMMN_TO}
		{dcl_input_date id=dateTo value=$VAL_DATETO}
		{/dcl_form_control}
		{dcl_form_control id=searchText controlsize=10 label="Issues/Summary"}
		{dcl_input_text id=searchText value=$VAL_SEARCHTEXT}
		{/dcl_form_control}
		{dcl_form_control id=tags controlsize=10 label=$smarty.const.STR_CMMN_TAGS help=$smarty.const.STR_CMMN_TAGSHELP}
		{dcl_input_text id=tags value=$VAL_TAGS}
		{/dcl_form_control}
	</fieldset>
	<fieldset id="divReport">
		<legend>{$smarty.const.STR_TCK_REPORTOPTIONS}</legend>
		{dcl_form_control id=title controlsize=10 label=$smarty.const.STR_CMMN_TITLE}
		{dcl_input_text id=title value=$VAL_REPORTTITLE}
		{/dcl_form_control}
		<div class="row">
			<div class="col-sm-4">
				<label for="acols">{$smarty.const.STR_CMMN_AVAILCOLS}:</label>
				<select class="form-control no-select2" name="acols" multiple size="8">
					{foreach from=$VAL_COLS item=item key=key}
						<option value="{$key|escape}">{$item|escape}</option>
					{/foreach}
				</select>
			</div>
			<div class="col-sm-2">
				<div class="row"><input class="btn btn-default btn-block" type="button" onclick="addColumn(this.form);" value="{$smarty.const.STR_CMMN_COL}"></div>
				<div class="row"><input class="btn btn-default btn-block" type="button" onclick="addGroup(this.form);" value="{$smarty.const.STR_CMMN_GRP}"></div>
			</div>
			<div class="col-sm-4">
				<label for="columnList">{$smarty.const.STR_CMMN_SHOWCOLS}:</label>
				<input type="hidden" name="columns" value="">
				<input type="hidden" name="columnhdrs" value="">
				<select class="form-control no-select2" name="columnList" multiple size="8">
					{foreach from=$VAL_SHOW item=item key=key}
						<option value="{$key|escape}">{$item|escape}</option>
					{/foreach}
				</select>
			</div>
			<div class="col-sm-2">
				<div class="row"><input class="btn btn-default btn-block" type="button" onclick="addOrder(this.form);" value="{$smarty.const.STR_CMMN_SRT}"></div>
				<div class="row"><input class="btn btn-default btn-block" type="button" onclick="removeColumn(this.form);" value="{$smarty.const.STR_CMMN_RM}"></div>
				<div class="row"><input class="btn btn-default btn-block" type="button" onclick="moveColumnUp(this.form);" value="{$smarty.const.STR_CMMN_UP}"></div>
				<div class="row"><input class="btn btn-default btn-block" type="button" onclick="moveColumnDown(this.form);" value="{$smarty.const.STR_CMMN_DN}"></div>
			</div>
		</div>
		<div class="row top 12">
			<div class="col-sm-4">
				<label for="groupList">{$smarty.const.STR_CMMN_GROUPING}:</label>
				<input type="hidden" name="groups" value="">
				<select class="form-control no-select2" name="groupList" multiple size="8">
					{foreach from=$VAL_GROUP item=item key=key}
						<option value="{$key|escape}">{$item|escape}</option>
					{/foreach}
				</select>
			</div>
			<div class="col-sm-2">
				<div class="row"><input class="btn btn-default btn-block" type="button" onclick="removeGroup(this.form);" value="{$smarty.const.STR_CMMN_RM}"></div>
				<div class="row"><input class="btn btn-default btn-block" type="button" onclick="moveGroupUp(this.form);" value="{$smarty.const.STR_CMMN_UP}"></div>
				<div class="row"><input class="btn btn-default btn-block" type="button" onclick="moveGroupDown(this.form);" value="{$smarty.const.STR_CMMN_DN}"></div>
			</div>
			<div class="col-sm-4">
				<label for="orderList">{$smarty.const.STR_CMMN_SORTING}:</label>
				<input type="hidden" name="order" value="">
				<select class="form-control no-select2" name="orderList" multiple size="8">
					{foreach from=$VAL_SORT item=item key=key}
						<option value="{$item.value|escape}">{$item.text|escape}</option>
					{/foreach}
				</select>
			</div>
			<div class="col-sm-2">
				<div class="row"><input class="btn btn-default btn-block" type="button" onclick="removeOrder(this.form);" value="{$smarty.const.STR_CMMN_RM}"></div>
				<div class="row"><input class="btn btn-default btn-block" type="button" onclick="moveOrderUp(this.form);" value="{$smarty.const.STR_CMMN_UP}"></div>
				<div class="row"><input class="btn btn-default btn-block" type="button" onclick="moveOrderDown(this.form);" value="{$smarty.const.STR_CMMN_DN}"></div>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<div class="row top12">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" onclick="doSearch(this.form);" value="{$smarty.const.STR_CMMN_SEARCH}">
				<input class="btn btn-link" type="reset" value="{$smarty.const.STR_CMMN_RESET}">
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript">
	$(function() {
		$("#content").find("select:not(.no-select2)").select2({ minimumResultsForSearch: 10 });
		$("input[data-input-type=date]").datepicker();
	});

	var f = document.forms["mondosearchform"];

	function selectDefault(sVal, sName, sParent, fCallback)
	{
		var a = sVal.split(':');
		var c = f.elements[sName];
		var p = f.elements[sParent];
		for (var j = 0; j < a.length; j++)//>
		{
			for (var m = 0; m < p.length; m++)//>
			{
				if (p.options[m].value == a[j].split(',')[0] && p.options[m].selected == false)
				{
					p.options[m].selected = true;
					fCallback(f);
					break;
				}
			}

			for (var i = 0; i < c.length; i++)//>
			{
				if (c.options[i].value == a[j])
				{
					c.options[i].selected = true;
					break;
				}
			}
		}
	}

	if (f)
	{
		f.elements['product[]'].onchange = function() { if (typeof(chgModule) == "function") chgModule(f); }

		f.elements['dcl_status_type[]'].onchange = function() { if (typeof(chgStatusType) == "function") chgStatusType(f); }

		{if !$IS_PUBLIC}f.elements['department[]'].onchange = function() { if (typeof(chgDepartment) == "function") chgDepartment(f); }{/if}

		if (typeof(chgStatusType) == "function")
		{
			chgStatusType(f);
			selectDefault("{$VAL_SELECTSTATUSKEY}", "status[]", "dcl_status_type[]", chgStatusType);
		}
{if !$IS_PUBLIC}
		if (typeof(chgDepartment) == "function")
		{
			chgDepartment(f);
			selectDefault("{$VAL_SELECTPERSONNELKEY}", "personnel[]", "department[]", chgDepartment);
		}
{/if}
		if (typeof(chgModule) == "function")
		{
			chgModule(f);
			selectDefault("{$VAL_SELECTMODULEKEY}", "module_id[]", "product[]", chgModule);
		}
	}

</script>
