<script language="JavaScript">
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
</script>
<form class="form" name="mapping" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="AttributeSetMap.Update">
	<input type="hidden" name="setid" value="{$VAL_SETID}">
	<input type="hidden" name="typeid" value="{$VAL_TYPEID}">
	<input type="hidden" name="keyidset" value="">
	<fieldset>
		<legend>{$smarty.const.STR_ATTR_EDITATTRIBUTESET|escape} [{$VAL_NAME|escape}] {$smarty.const.STR_ATTR_TYPE|escape} [{$VAL_TYPE|escape}]</legend>
		<div class="col-md-8">
			<div class="col-md-4"><label for="src">{$smarty.const.STR_ATTR_AVAILABLEVALUES|escape}</label></div>
			<div class="col-md-4 col-md-offset-2"><label for="used">{$smarty.const.STR_ATTR_USEDVALUES|escape}</label></div>
		</div>
		<div class="col-md-8">
			<div class="col-md-4">
				<select class="form-control" multiple size="20" name="src">
				{$OPT_AVAILABLE}
				</select>
			</div>
			<div class="col-md-2">
				<input class="btn btn-default btn-block" type="button" onclick="addAll(this.form);" value="{$smarty.const.STR_CMMN_ALL|escape} &gt;&gt;">
				<input class="btn btn-default btn-block" type="button" onclick="addValue(this.form);" value="{$smarty.const.STR_CMMN_SEL|escape} &gt;">
				<input class="btn btn-default btn-block" type="button" onclick="removeValue(this.form);" value="{$smarty.const.STR_CMMN_SEL|escape} &lt;">
				<input class="btn btn-default btn-block" type="button" onclick="removeAll(this.form);" value="{$smarty.const.STR_CMMN_ALL|escape} &lt;&lt;">
			</div>
			<div class="col-md-4">
				<select class="form-control" multiple size="20" name="used">
				{$OPT_SELECTED}
				</select>
			</div>
{if $IS_WEIGHTED}
			<div class="col-md-2">
				<input class="btn btn-default btn-block" type="button" onclick="moveUp(this.form);" value="{$smarty.const.STR_CMMN_UP|escape}">
				<input class="btn btn-default btn-block" type="button" onclick="moveDown(this.form);" value="{$smarty.const.STR_CMMN_DN|escape}">
			</div>
{/if}
		</div>
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-md-offset-1">
				<input class="btn btn-primary" type="button" onclick="submitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE|escape}">
				<input class="btn btn-link" type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=AttributeSetMap.Index&id={$VAL_SETID}';" value="{$smarty.const.STR_CMMN_CANCEL|escape}">
			</div>
		</div>
	</fieldset>
</form>