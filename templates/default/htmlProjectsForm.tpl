<!-- $Id: htmlProjectsForm.tpl,v 1.4 2006/11/27 06:00:52 mdean Exp $ -->
{dcl_calendar_init}
{dcl_validator_init}
<!-- Form Validation -->
<script language="JavaScript">
{literal}
function validateAndSubmitForm(form)
{
{/literal}
		var aValidators = new Array(
			new ValidatorInteger(form.elements["reportto"], "{$smarty.const.STR_PRJ_LEAD}"),
			new ValidatorSelection(form.elements["status"], "{$smarty.const.STR_PRJ_STATUS}"),
			new ValidatorString(form.elements["name"], "{$smarty.const.STR_PRJ_NAME}"),
			new ValidatorString(form.elements["description"], "{$smarty.const.STR_PRJ_DESCRIPTION}")
		);
{literal}
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
{/literal}
</script>
{if $XML_TEMPLATES}
<script language="JavaScript1.1" type="text/javascript">
	var params = new Array();
	var templ = new Array();

{$JS_TEMPLATE}
{literal}
function getSelectedTemplate(f){
	var tsel="";
	var i;
	for (i=0;i<f.template.length;i++){
		if(f.template[i].selected){
			tsel=f.template[i].value;
			break;
		}
	}
	return tsel;
}
function getSelectedValue(f){
	var vsel="";
	var i;
	for (i=0;i<f.value.length;i++){
		if(f.value[i].selected){
			vsel=f.value[i].value;
			break;
		}
	}
	return vsel;
}	
function getDataSource(tmpl,varname){
	var psrc="";
	var i;
	for (i=0;i<templ[tmpl].length;i++){
		if(templ[tmpl][i]['varname']==varname){
			psrc=templ[tmpl][i]['source'];
			break;
		}
	}
	return psrc;
}	
function getSelectedParam(f){
	var psel="";
	var i;
	for (i=0;i<f.param.length;i++){
		if(f.param[i].selected){
			psel=f.param[i].value;
			break;
		}
	}
	return psel;
}
function changeTemplate(f){
	var i;
	var j;
	var tsel;
	f.param.length = 0;
	f.value.length = 0;
	tsel=getSelectedTemplate(f);
	if (typeof(f.selectedparams) != 'array') f.selectedparams=new Array();
	f.selectedparams.length=0;
	if (tsel != '0'){
		for (i=0;i<templ[tsel].length;i++){
			var l=f.param.length;
			f.param[l]=new Option(templ[tsel][i]['text'],templ[tsel][i]['varname']);
			f.selectedparams[templ[tsel][i]['varname']] = 0;
		}
	}
	if (f.param.length) changeParam(f);
}		
function changeParam(f){
	var i;
	var psel;
	var tsel;
	f.value.length = 0;
	tsel=getSelectedTemplate(f);
	psel=getSelectedParam(f);
	var psrc;
	psrc=getDataSource(tsel,psel);
	for (i=0;i<params[psrc].length;i++){
		var l=f.value.length;
		f.value[l]=new Option(params[psrc][i]['text'],params[psrc][i]['id']);
		if (params[psrc][i]['id'] == f.selectedparams[psel]){
			f.value[l].selected=true;
		}
	}
}	
function changeValue(f){
	var psel;
	var vsel;
	psel=getSelectedParam(f);
	vsel=getSelectedValue(f);
	f.selectedparams[psel] = vsel;
	var encodedValue="";
	for (var i=0;i<f.param.length;i++){
		if (encodedValue > "") encodedValue+='&';
		encodedValue += f.param[i].value + '=' + f.selectedparams[f.param[i].value];
	}
	f.encodedparams.value=encodedValue;
}
{/literal}
</script>
{/if}
<form class="styled" name="PROJECTFORM" method="post" action="{$URL_MAIN_PHP}">
{if $IS_EDIT}
	<input type="hidden" name="menuAction" value="boProjects.dbmodify">
	<input type="hidden" name="projectid" value="{$VAL_PROJECTID}">
	<fieldset>
		<legend>{$smarty.const.STR_PRJ_EDIT}</legend>
{else}
	<input type="hidden" name="menuAction" value="boProjects.dbnewproject">
	<fieldset>
		<legend>{$smarty.const.STR_PRJ_ADD}</legend>
{/if}
		<div class="required">
			<label for="name">{$smarty.const.STR_PRJ_NAME}:</label>
			<input type="text" size="50" maxlength="100" id="name" name="name" value="{$VAL_NAME|escape}">
		</div>
{if $IS_EDIT}
		<div class="required">
			<label for="status">{$smarty.const.STR_PRJ_STATUS}:</label>
			{$CMB_STATUS}
		</div>
{/if}
		<div class="required">
			<label for="reportto">{$smarty.const.STR_PRJ_LEAD}:</label>
			{$CMB_REPORTTO}
		</div>
		<div>
			<label for="deadline">{$smarty.const.STR_PRJ_DEADLINE}:</label>
			{dcl_calendar name="projectdeadline" value="$VAL_PROJECTDEADLINE"}
		</div>
		<div>
			<label for="parentprojectid">{$smarty.const.STR_PRJ_PARENTPRJ}:</label>
			{$CMB_PARENTPRJ}
		</div>
		<div class="required">
			<label for="description">{$smarty.const.STR_PRJ_DESCRIPTION}:</label>
			<textarea name="description" rows="4" cols="70" wrap valign="top">{$VAL_DESCRIPTION|escape}</textarea>
		</div>
	</fieldset>
{if $XML_TEMPLATES}
	<fieldset>
		<legend>{$smarty.const.STR_PRJ_TEMPLATE}</legend>
		<div>
			<label for="">{$smarty.const.STR_PRJ_USETMPL}:</label>
			{$CMB_XMLPROJECTS}
		</div>
		<div>
			<label for="param">{$smarty.const.STR_PRJ_TMPLPARAM}:</label>
			<select id="param" name="param" onchange="changeParam(this.form);"><option>{$smarty.const.STR_PRJ_NOTMPL}</option></select>
		</div>
		<div>
			<label for="value">{$smarty.const.STR_PRJ_TMPLVALUE}:</label>
			<select id="value" name="value" onchange="changeValue(this.form);"><option>{$smarty.const.STR_PRJ_NOTMPL}</option></select>
		</div>
	</fieldset>
{/if}
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="reset" value="{$smarty.const.STR_CMMN_RESET}">
		</div>
	</fieldset>
</form>