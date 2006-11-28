<!-- $Id: htmlAttributesetmapping.tpl,v 1.4 2006/11/27 06:00:51 mdean Exp $ -->
<script language="JavaScript">{literal}
function submitForm(f) {
	f.elements["keyidset"].value = "";
	_selectAll(f.elements["used"], f.elements["keyidset"], null);
	f.submit();
}

function _selectAll(c, h) {
	h.value = "";
	if (c.options.length > 0) {
		for (var i = 0; i < c.options.length; i++) {
			if (i > 0)
				h.value += ",";
			h.value += c.options[i].value;
		}
	}
}

function _moveSelected(from, to, bMove) {
	if (from.options.length > 0) {
		for (var i = 0; i < from.options.length; i++) {
			while (i < from.options.length && from.options[i].selected == true) {
				var o = new Option();
				o.value = from.options[i].value;
				o.text = from.options[i].text;
				to.options[to.options.length] = o;
				from.options[i] = null;
			}
		}
	}	
}

function _moveAll(from, to) {
	if (from.options.length > 0) {
		while (from.options.length > 0) {
			var o = new Option();
			o.value = from.options[0].value;
			o.text = from.options[0].text;
			to.options[to.options.length] = o;
			from.options[0] = null;
		}	
	}
}

function _moveUp(c) {
	var idx = c.selectedIndex;
	if (idx == -1 || idx == 0) // Can't move up or nothing selected...
		return;
	
	// There can be only one!
	if (idx < c.options.length - 1) {
		var cnt = 1;
		var testidx = idx + 1;
		while (cnt == 1 && testidx < c.options.length) {
			if (c.options[testidx].selected == true) {
				cnt++;
			}
			testidx++;
		}
		if (cnt > 1) {
			alert("You can move only one item at a time!");
			return;
		}
	}
	
	var arrNew = new Array();
	for (var i = 0; i < c.options.length; i++) {
		if (i < idx - 1 || i > idx) {
			arrNew[arrNew.length] = new Option();
			arrNew[arrNew.length - 1].value = c.options[i].value;
			arrNew[arrNew.length - 1].text = c.options[i].text;
		}
		else if (i == idx - 1){
				arrNew[arrNew.length] = new Option();
				arrNew[arrNew.length - 1].value = c.options[idx].value;
				arrNew[arrNew.length - 1].text = c.options[idx].text;
				arrNew[arrNew.length] = new Option();
				arrNew[arrNew.length - 1].value = c.options[i].value;
				arrNew[arrNew.length - 1].text = c.options[i].text;
				i++; // Skip the selected one
			}
	}
	c.options.length = 0;
	for (i = 0; i < arrNew.length; i++) {
		c.options[i] = arrNew[i];
		if (i == idx - 1) {
			c.options[i].selected = true;
		}
	}
}

function _moveDown(c) {
	var idx = c.selectedIndex;
	if (idx == -1 || idx == (c.options.length - 1)) // Can't move down or nothing selected
		return;
	
	// There can be only one!
	if (idx < c.options.length - 1) {
		var cnt = 1;
		var testidx = idx + 1;
		while (cnt == 1 && testidx < c.options.length) {
			if (c.options[testidx].selected == true) {
				cnt++;
			}
			testidx++;
		}
		if (cnt > 1) {
			alert("You can move only one item at a time!");
			return;
		}
	}
	
	var arrNew = new Array();
	for (var i = 0; i < c.options.length; i++) {
		if (i < idx || i > idx + 1) {
			arrNew[arrNew.length] = new Option();
			arrNew[arrNew.length - 1].value = c.options[i].value;
			arrNew[arrNew.length - 1].text = c.options[i].text;
		}
		else if (i == idx){
				arrNew[arrNew.length] = new Option();
				arrNew[arrNew.length - 1].value = c.options[i + 1].value;
				arrNew[arrNew.length - 1].text = c.options[i + 1].text;
				arrNew[arrNew.length] = new Option();
				arrNew[arrNew.length - 1].value = c.options[i].value;
				arrNew[arrNew.length - 1].text = c.options[i].text;
				i++; // Skip the selected one
			}
	}
	c.options.length = 0;
	for (i = 0; i < arrNew.length; i++) {
		c.options[i] = arrNew[i];
		if (i == idx + 1) {
			c.options[i].selected = true;
		}
	}
}

function moveUp(f) {
	_moveUp(f.elements["used"]);
}

function moveDown(f) {
	_moveDown(f.elements["used"]);
}

function addValue(f) {
	if (f.elements["src"].selectedIndex == -1)
		return;
	
	_moveSelected(f.elements["src"], f.elements["used"]);
}

function removeValue(f) {
	if (f.elements["used"].selectedIndex == -1)
		return;
	
	_moveSelected(f.elements["used"], f.elements["src"]);
}

function addAll(f) {
	if (f.elements["src"].options.length < 1)
		return;
	
	_moveAll(f.elements["src"], f.elements["used"]);
}

function removeAll(f) {
	if (f.elements["used"].options.length < 1)
		return;
	
	_moveAll(f.elements["used"], f.elements["src"]);
}
{/literal}</script>
<form class="styled" name="mapping" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="boAttributesets.dbmap">
	<input type="hidden" name="setid" value="{$VAL_SETID}">
	<input type="hidden" name="typeid" value="{$VAL_TYPEID}">
	<input type="hidden" name="keyidset" value="">
	<fieldset>
		<legend>{$smarty.const.STR_ATTR_EDITATTRIBUTESET} [{$VAL_NAME}] {$smarty.const.STR_ATTR_TYPE} [{$VAL_TYPE}]</legend>
		<div class="input">
			<label for="src">{$smarty.const.STR_ATTR_AVAILABLEVALUES}</label>
			<select multiple size="8" name="src">
			{$OPT_AVAILABLE}
			</select>
		</div>
		<div class="command">
			<input type="button" onclick="addAll(this.form);" value="{$smarty.const.STR_CMMN_ALL} &gt;&gt;">
			<input type="button" onclick="addValue(this.form);" value="{$smarty.const.STR_CMMN_SEL} &gt;">
			<input type="button" onclick="removeValue(this.form);" value="{$smarty.const.STR_CMMN_SEL} &lt;">
			<input type="button" onclick="removeAll(this.form);" value="{$smarty.const.STR_CMMN_ALL} &lt;&lt;">
		</div>
		<div class="input">
			<label for="used">{$smarty.const.STR_ATTR_USEDVALUES}</label>
			<select multiple size="8" name="used">
			{$OPT_SELECTED}
			</select>
		</div>
{if $IS_WEIGHTED}
		<div class="command">
			<input type="button" onclick="moveUp(this.form);" value="{$smarty.const.STR_CMMN_UP}">
			<input type="button" onclick="moveDown(this.form);" value="{$smarty.const.STR_CMMN_DN}">
		</div>
{/if}
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="submitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=boAttributesets.view&id={$VAL_SETID}';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>