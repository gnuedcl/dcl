{dcl_calendar_init}
<script language="JavaScript" src="js/ticketsearch.js"></script>
<script language="JavaScript">

	var iSection = 0;
	var sLastLayer = '{if !$IS_PUBLIC}divPersonnel{else}divProduct{/if}';
	function showHide(sLayer)
	{
		if (sLastLayer == sLayer)
			return;

		var oDiv = document.getElementById(sLayer);
		var oDivCurrent = document.getElementById(sLastLayer);

		if (oDivCurrent)
			oDivCurrent.style.display = 'none';

		if (oDiv)
			oDiv.style.display = '';

		sLastLayer = sLayer;
	}

	function getDates()
	{
		var f = document.forms["mondosearchform"];
		var sSummary = "";
		if (f.elements["createdon"].checked ||
			f.elements["closedon"].checked ||
			f.elements["statuson"].checked ||
			f.elements["lastactionon"].checked)
		{

			sSummary += '<tr><th nowrap style="width: 5%; text-align: left; vertical-align: top;">Dates*:</th><td>';
			sSummary += '{$smarty.const.STR_CMMN_FROM} ' + f.elements["dateFrom"].value + ' {$smarty.const.STR_CMMN_TO} ' + f.elements["dateTo"].value + ': ';
			if (f.elements["createdon"].checked) sSummary += "{$smarty.const.STR_TCK_OPENEDON}; ";
			if (f.elements["closedon"].checked) sSummary += "{$smarty.const.STR_TCK_CLOSEDON}; ";
			if (f.elements["statuson"].checked) sSummary += "{$smarty.const.STR_TCK_STATUSON}; ";
			if (f.elements["lastactionon"].checked) sSummary += "{$smarty.const.STR_TCK_LASTACTION}; ";
			sSummary += '</td></tr>';

		}

		return sSummary;
	}

	function getTags()
	{
		var f = document.forms["mondosearchform"];
		var sTags = "";
		if (f.elements["tags"].value != "")
		{

			sTags += '<tr><th nowrap style="width: 5%; text-align: left; vertical-align: top;">{$smarty.const.STR_CMMN_TAGS}:</th><td>';
			sTags += f.elements["tags"].value;
			sTags += '</td></tr>';

		}
		
		return sTags;
	}

	function getTextSearch()
	{
		var f = document.forms["mondosearchform"];
		var sSummary = "";
		if (f.elements["searchText"].value != "")
		{
			sSummary += '<tr><th nowrap style="width: 5%; text-align: left; vertical-align: top;">Text:</th><td>';
			sSummary += f.elements["searchText"].value;
			sSummary += '</td></tr>';
		}

		return sSummary;
	}
{if !$IS_PUBLIC}
	function getPersonnel()
	{
		var f = document.forms["mondosearchform"];
		var c = f.elements["personnel[]"];
		var d = f.elements["department[]"];
		var sSummary = "";
		if ((f.elements["responsible"].checked ||
			f.elements["createdby"].checked ||
			f.elements["closedby"].checked))
		{
			if (d.selectedIndex > -1)
			{
				sSummary += '<tr><th nowrap style="width: 5%; text-align: left; vertical-align: top;">Department:</th><td><b>';
				if (f.elements["responsible"].checked)
					sSummary += '{$smarty.const.STR_TCK_RESPONSIBLE}, ';

				if (f.elements["createdby"].checked)
					sSummary += '{$smarty.const.STR_TCK_OPENEDBY}, ';

				if (f.elements["closedby"].checked)
					sSummary += '{$smarty.const.STR_TCK_CLOSEDBY}';

				sSummary += ':&nbsp;</b>';

				for (var i = d.selectedIndex; i < d.options.length; i++)
				{
					if (d.options[i].selected)
						sSummary += d.options[i].text + '; ';
				}
				sSummary += '</td></tr>';
			}

			if (c.selectedIndex > -1)
			{
				sSummary += '<tr><th nowrap style="width: 5%; text-align: left; vertical-align: top;">Personnel:</th><td><b>';
				if (f.elements["responsible"].checked)
					sSummary += '{$smarty.const.STR_TCK_RESPONSIBLE}, ';

				if (f.elements["createdby"].checked)
					sSummary += '{$smarty.const.STR_TCK_OPENEDBY}, ';

				if (f.elements["closedby"].checked)
					sSummary += '{$smarty.const.STR_TCK_CLOSEDBY}';

				sSummary += ':&nbsp;</b>';

				for (var i = c.selectedIndex; i < c.options.length; i++)
				{
					if (c.options[i].selected)
						sSummary += c.options[i].text + '; ';
				}
				sSummary += '</td></tr>';
			}
		}

		return sSummary;
	}
{/if}
	function getSelections(c, sTitle)
	{
		if (!c)
			return "";

		var sSummary = "";
		if (c.selectedIndex > -1)
		{
			sSummary += '<tr><th nowrap style="width: 5%; text-align: left; vertical-align: top;">' + sTitle + ':</th><td>';
			for (var i = c.selectedIndex; i < c.options.length; i++)
			{
				if (c.options[i].selected)
					sSummary += c.options[i].text + '; ';
			}

			sSummary += '</td></tr>';
		}

		return sSummary;
	}

	function updateSummary()
	{
		var oDiv = document.getElementById("summary");
		if (!oDiv)
			return;

		var sSummary = '<table width="100%" border="0">';
		var f = document.forms["mondosearchform"];
		if (f)
		{

			{if !$IS_PUBLIC}sSummary += getPersonnel();{/if}
			sSummary += getSelections(f.elements["product[]"], "{$smarty.const.STR_TCK_PRODUCT}");
			sSummary += getSelections(f.elements["module_id[]"], "{$smarty.const.STR_CMMN_MODULE}");
			sSummary += getSelections(f.elements["account[]"], "{$smarty.const.STR_TCK_ACCOUNT}");
			sSummary += getSelections(f.elements["priority[]"], "{$smarty.const.STR_TCK_PRIORITY}");
			sSummary += getSelections(f.elements["type[]"], "{$smarty.const.STR_TCK_TYPE}");
			sSummary += getSelections(f.elements["status[]"], "{$smarty.const.STR_TCK_STATUS}");
			sSummary += getSelections(f.elements["dcl_status_type[]"], "{$smarty.const.STR_CMMN_STATUSTYPE}");
			{if !$IS_PUBLIC}sSummary += getSelections(f.elements["is_public[]"], "{$smarty.const.STR_CMMN_PUBLIC}");{/if}
			sSummary += getSelections(f.elements["entity_source_id[]"], "{$smarty.const.STR_CMMN_SOURCE}");
			sSummary += getDates();
			sSummary += getTags();
			sSummary += getTextSearch();

		}

		sSummary += "</table>";

		oDiv.innerHTML = sSummary;
	}

</script>
<form class="styled" name="mondosearchform" action="{$URL_MAIN_PHP}" method="post">
	<input type="hidden" name="menuAction" value="boTickets.dbsearch">
	<fieldset>
		<legend>{$smarty.const.STR_TCK_SEARCHTITLE}</legend>
		<div>
			<fieldset>
				<legend>Criteria</legend>
				<div id="summary" class="scrollable"></div>
			</fieldset>
		</div>
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_CMMN_OPTIONS}</legend>
		<div>
		<fieldset>
			<div class="menu">
				<ul>{strip}
					{if !$IS_PUBLIC}<li class="first"><a href="javascript:showHide('divPersonnel');">Personnel</a></li>{/if}
					<li{if $IS_PUBLIC} class="first"{/if}><a href="javascript:showHide('divProduct');">{$smarty.const.STR_TCK_PRODUCT}</a></li>
					<li><a href="javascript:showHide('divAccount');">{$smarty.const.STR_TCK_ACCOUNT}</a></li>
					<li><a href="javascript:showHide('divPriority');">{$smarty.const.STR_TCK_PRIORITY}</a></li>
					<li><a href="javascript:showHide('divType');">{$smarty.const.STR_TCK_TYPE}</a></li>
					<li><a href="javascript:showHide('divStatus');">{$smarty.const.STR_TCK_STATUS}</a></li>
				</ul>
			</div>
			<div class="menu">
				<ul>
					{if !$IS_PUBLIC}<li class="first"><a href="javascript:showHide('divPublic');">{$smarty.const.STR_CMMN_PUBLIC}</a></li>{/if}
					<li{if $IS_PUBLIC} class="first"{/if}><a href="javascript:showHide('divSource');">{$smarty.const.STR_CMMN_SOURCE}</a></li>
					<li><a href="javascript:showHide('divDate');">Dates</a></li>
					<li><a href="javascript:showHide('divText');">Text</a></li>
					<li><a href="javascript:showHide('divTags');">{$smarty.const.STR_CMMN_TAGS}</a></li>
					<li><a href="javascript:showHide('divReport');">{$smarty.const.STR_TCK_REPORTOPTIONS}</a></li>
				{/strip}</ul>
			</div>
		</fieldset>
		</div>
	</fieldset>
	{if !$IS_PUBLIC}<fieldset id="divPersonnel">
		<legend>Personnel</legend>
		<div>
			<fieldset>
				<div>
					<label><input type="checkbox" id="responsible" name="responsible" value="1"{$CHK_RESPONSIBLE}>{$smarty.const.STR_TCK_RESPONSIBLE}</label>
					<label><input type="checkbox" id="createdby" name="createby" value="1"{$CHK_CREATEDBY}>{$smarty.const.STR_TCK_OPENEDBY}</label>
					<label><input type="checkbox" id="closedby" name="closedby" value="1"{$CHK_CLOSEDBY}>{$smarty.const.STR_TCK_CLOSEDBY}</label>
				</div>
			</fieldset>
		</div>
		<div class="input">
			<label for="department">Department:</label>
			{dcl_select_department name=department default=$VAL_DEPARTMENTS size=8}
		</div>
		<div class="input">
			<label for="personnel">Personnel:</label>
			{dcl_select_personnel name=personnel default=$VAL_PERSONNEL size=8}
		</div>
	</fieldset>{/if}
	<fieldset id="divProduct"{if !$IS_PUBLIC} style="display: none;"{/if}>
		<legend>{$smarty.const.STR_TCK_PRODUCT}</legend>
		<div class="input">
			<label for="product">{$smarty.const.STR_TCK_PRODUCT}:</label>
			{dcl_select_product default=$VAL_PRODUCT size=8 active=N}
		</div>
		<div class="input">
			<label for="module_id">{$smarty.const.STR_CMMN_MODULE}:</label>
			{dcl_select_module default=$VAL_MODULE size=8 active=N}
		</div>
	</fieldset>
	<fieldset id="divAccount" style="display: none;">
		<legend>{$smarty.const.STR_TCK_ACCOUNT}</legend>
		<div>{$CMB_ACCOUNTS}</div>
	</fieldset>
	<fieldset id="divPriority" style="display: none;">
		<legend>{$smarty.const.STR_TCK_PRIORITY}</legend>
		<div>{dcl_select_priority default=$VAL_PRIORITY size=8}</div>
	</fieldset>
	<fieldset id="divType" style="display: none;">
		<legend>{$smarty.const.STR_TCK_TYPE}</legend>
		<div>{dcl_select_severity default=$VAL_TYPE size=8 name=type}</div>
	</fieldset>
	<fieldset id="divStatus" style="display: none;">
		<legend>{$smarty.const.STR_TCK_STATUS}</legend>
		<div class="input">
			<label>{$smarty.const.STR_CMMN_STATUSTYPE}:</label>
			{$CMB_STATUSTYPES}
		</div>
		<div class="input">
			<label for="status">{$smarty.const.STR_TCK_STATUS}:</label>
			{$CMB_STATUSESEMPTY}
		</div>
	</fieldset>
	{if !$IS_PUBLIC}<fieldset id="divPublic" style="display: none;">
		<legend>{$smarty.const.STR_CMMN_PUBLIC}</legend>
		<div>{$CMB_PUBLIC}</div>
	</fieldset>{/if}
	<fieldset id="divSource" style="display: none;">
		<legend>{$smarty.const.STR_CMMN_SOURCE}</legend>
		<div>{dcl_select_source default=$VAL_SOURCE size=8}</div>
	</fieldset>
	<fieldset id="divDate" style="display: none;">
		<legend>Dates</legend>
		<div>
			<fieldset>
				<div>
					<label><input type="checkbox" id="createdon" name="createdon" value="1"{$CHK_CREATEDON}>{$smarty.const.STR_TCK_OPENEDON}</label>
					<label><input type="checkbox" id="closedon" name="closedon" value="1"{$CHK_CLOSEDON}>{$smarty.const.STR_TCK_CLOSEDON}</label>
					<label><input type="checkbox" id="statuson" name="statuson" value="1"{$CHK_STATUSON}>{$smarty.const.STR_TCK_STATUSON}</label>
					<label><input type="checkbox" id="lastactionon" name="lastactionon" value="1"{$CHK_LASTACTIONON}>{$smarty.const.STR_TCK_LASTACTIONON}</label>
				</div>
			</fieldset>
		</div>
		<div>
			<label for="dateFrom">{$smarty.const.STR_CMMN_FROM}:</label>
			{dcl_calendar name=dateFrom value=$VAL_DATEFROM}
		</div>
		<div>
			<label for="dateTo">{$smarty.const.STR_CMMN_TO}:</label>
			{dcl_calendar name=dateTo value=$VAL_DATETO}
		</div>
	</fieldset>
	<fieldset id="divText" style="display: none;">
		<legend>Text</legend>
		<div><label for="searchText">Issues/Summary:</label><input type="text" size="50" id="searchText" name="searchText" value="{$VAL_SEARCHTEXT}"></div>
	</fieldset>
	<fieldset id="divTags" style="display: none;">
		<legend>{$smarty.const.STR_CMMN_TAGS}</legend>
		<div>
			<label for="tags">{$smarty.const.STR_CMMN_TAGS}:</label>
			<input type="text" size="50" id="tags" name="tags" value="{$VAL_TAGS|escape}">
			<span>{$smarty.const.STR_CMMN_TAGSHELP|escape}</span>
		</div>
	</fieldset>
	<fieldset id="divReport" style="display: none;">
		<legend>{$smarty.const.STR_TCK_REPORTOPTIONS}</legend>
		<div>
			<label for="title">{$smarty.const.STR_CMMN_TITLE}:</label>
			<input type="text" size="50" id="title" name="title" value="{$VAL_REPORTTITLE}">
		</div>
		<div>
			<div class="input">
				<label for="acols">{$smarty.const.STR_CMMN_AVAILCOLS}:</label>
				<select name="acols" multiple size="8">
				{foreach from=$VAL_COLS item=item key=key}
					<option value="{$key}">{$item}</option>
				{/foreach}
				</select>
			</div>
			<div class="command">
				<input type="button" onclick="addColumn(this.form);" value="{$smarty.const.STR_CMMN_COL}">
				<input type="button" onclick="addGroup(this.form);" value="{$smarty.const.STR_CMMN_GRP}">
			</div>
			<div class="input">
				<label for="columnList">{$smarty.const.STR_CMMN_SHOWCOLS}:</label>
				<input type="hidden" name="columns" value="">
				<input type="hidden" name="columnhdrs" value="">
				<select name="columnList" multiple size="8">
				{foreach from=$VAL_SHOW item=item key=key}
					<option value="{$key}">{$item}</option>
				{/foreach}
				</select>
			</div>
			<div class="command">
				<input type="button" onclick="addOrder(this.form);" value="{$smarty.const.STR_CMMN_SRT}">
				<input type="button" onclick="removeColumn(this.form);" value="{$smarty.const.STR_CMMN_RM}">
				<input type="button" onclick="moveColumnUp(this.form);" value="{$smarty.const.STR_CMMN_UP}">
				<input type="button" onclick="moveColumnDown(this.form);" value="{$smarty.const.STR_CMMN_DN}">
			</div>
		</div>
		<div>
			<div class="input">
				<label for="groupList">{$smarty.const.STR_CMMN_GROUPING}:</label>
				<input type="hidden" name="groups" value="">
				<select name="groupList" multiple size="8">
				{foreach from=$VAL_GROUP item=item key=key}
					<option value="{$key}">{$item}</option>
				{/foreach}
				</select>
			</div>
			<div class="command">
				<input type="button" onclick="removeGroup(this.form);" value="{$smarty.const.STR_CMMN_RM}">
				<input type="button" onclick="moveGroupUp(this.form);" value="{$smarty.const.STR_CMMN_UP}">
				<input type="button" onclick="moveGroupDown(this.form);" value="{$smarty.const.STR_CMMN_DN}">
			</div>
			<div class="input">
				<label for="orderList">{$smarty.const.STR_CMMN_SORTING}:</label>
				<input type="hidden" name="order" value="">
				<select name="orderList" multiple size="8">
				{foreach from=$VAL_SORT item=item key=key}
					<option value="{$key}">{$item}</option>
				{/foreach}
				</select>
			</div>
			<div class="command">
				<input type="button" onclick="removeOrder(this.form);" value="{$smarty.const.STR_CMMN_RM}">
				<input type="button" onclick="moveOrderUp(this.form);" value="{$smarty.const.STR_CMMN_UP}">
				<input type="button" onclick="moveOrderDown(this.form);" value="{$smarty.const.STR_CMMN_DN}">
			</div>
		</div>
	</fieldset>
	<fieldset><div class="submit"><input type="button" onclick="doSearch(this.form);" value="{$smarty.const.STR_CMMN_SEARCH}"><input type="reset" value="{$smarty.const.STR_CMMN_RESET}"></div></fieldset>
</form>
<script type="text/javascript">

	var f = document.forms["mondosearchform"];
	function setOnChangeEventHandler(c)
	{
		c.onchange = updateSummary;
	}

	function setOnClickEventHandler(c)
	{
		c.onclick = updateSummary;
	}

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
		// Select boxen
		{if !$IS_PUBLIC}setOnChangeEventHandler(f.elements["personnel[]"]);{/if}
		setOnChangeEventHandler(f.elements["priority[]"]);
		setOnChangeEventHandler(f.elements["type[]"]);
		setOnChangeEventHandler(f.elements["account[]"]);
		setOnChangeEventHandler(f.elements["status[]"]);
		setOnChangeEventHandler(f.elements["dcl_status_type[]"]);
		setOnChangeEventHandler(f.elements["module_id[]"]);
		{if !$IS_PUBLIC}setOnChangeEventHandler(f.elements["department[]"]);{/if}
		{if !$IS_PUBLIC}setOnChangeEventHandler(f.elements["is_public[]"]);{/if}
		setOnChangeEventHandler(f.elements["entity_source_id[]"]);

		// Modules are dependent on selected products, so we handle it a little differently
		f.elements['product[]'].onchange = function() { if (typeof(chgModule) == "function") chgModule(f); updateSummary(); }

		// Statuses now filter by type to reduce available selections
		f.elements['dcl_status_type[]'].onchange = function() { if (typeof(chgStatusType) == "function") chgStatusType(f); updateSummary(); }

		// Personnel now filter by department to reduce available selections
		{if !$IS_PUBLIC}f.elements['department[]'].onchange = function() { if (typeof(chgDepartment) == "function") chgDepartment(f); updateSummary(); }{/if}

		// Text boxen
		setOnChangeEventHandler(f.elements["searchText"]);

		// Tags
		setOnChangeEventHandler(f.elements["tags"]);

		// Checkboxen
		{if !$IS_PUBLIC}setOnClickEventHandler(f.elements["responsible"]);{/if}
		{if !$IS_PUBLIC}setOnClickEventHandler(f.elements["createdby"]);{/if}
		{if !$IS_PUBLIC}setOnClickEventHandler(f.elements["closedby"]);{/if}
		setOnClickEventHandler(f.elements["createdon"]);
		setOnClickEventHandler(f.elements["closedon"]);
		setOnClickEventHandler(f.elements["statuson"]);
		setOnClickEventHandler(f.elements["lastactionon"]);

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

		updateSummary();
	}

</script>
