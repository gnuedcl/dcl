function doSearch(f) {
	f.elements["columnhdrs"].value = "";
	selectAll(f.elements["groupList"], f.elements["groups"], f.elements["columnhdrs"]);
	selectAll(f.elements["columnList"], f.elements["columns"], f.elements["columnhdrs"]);
	selectAll(f.elements["orderList"], f.elements["order"], null);
	f.submit();
}

function selectAll(c, h, cpText) {
	h.value = "";
	if (c.options.length > 0) {
		var s = new String;
		if (cpText)
			s = cpText.value;
		for (var i = 0; i < c.options.length; i++) {
			if (i > 0)
				h.value += ",";
			h.value += c.options[i].value;
			if (cpText){
				if (s.length > 0)
					s += ",";
				s += c.options[i].text;
			}
		}
		if (cpText)
			cpText.value = s;
	}
}

function copySelected(from, to, bMove) {
	if (from.options.length > 0) {
		for (var i = 0; i < from.options.length; i++) {
			if (from.options[i].selected == true) {
				var o = new Option();
				o.value = from.options[i].value;
				o.text = from.options[i].text;
				to.options[to.options.length] = o;
			}
		}
	}	
}

function moveSelected(from, to, bMove) {
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

function removeSelected(from) {
	if (from.options.length > 0) {
		for (var i = 0; i < from.options.length; i++) {
			while (i < from.options.length && from.options[i].selected == true) {
				from.options[i] = null;
			}
		}
	}	
}

function addColumn(f) {
	if (f.elements["acols"].selectedIndex == -1)
		return;
	
	moveSelected(f.elements["acols"], f.elements["columnList"]);
}

function moveUp(c) {
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

function moveDown(c) {
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

function moveColumnUp(f) {
	moveUp(f.elements["columnList"]);
}

function moveColumnDown(f) {
	moveDown(f.elements["columnList"]);
}

function removeColumn(f) {
	if (f.elements["columnList"].selectedIndex == -1)
		return;
	
	if (f.elements["columnList"].options[f.elements["columnList"].selectedIndex].value == "ticketid") {
		alert("Ticket ID must appear in the column list!");
		return;
	}
	
	moveSelected(f.elements["columnList"], f.elements["acols"]);
}

function addGroup(f) {
	if (f.elements["acols"].selectedIndex == -1)
		return;
	
	moveSelected(f.elements["acols"], f.elements["groupList"]);
}

function moveGroupUp(f) {
	moveUp(f.elements["groupList"]);
}

function moveGroupDown(f) {
	moveDown(f.elements["groupList"]);
}

function removeGroup(f) {
	if (f.elements["groupList"].selectedIndex == -1)
		return;
	
	moveSelected(f.elements["groupList"], f.elements["acols"]);
}

function addOrder(f) {
	if (f.elements["columnList"].selectedIndex == -1)
		return;
	
	copySelected(f.elements["columnList"], f.elements["orderList"]);
}

function moveOrderUp(f) {
	moveUp(f.elements["orderList"]);
}

function moveOrderDown(f) {
	moveDown(f.elements["orderList"]);
}

function removeOrder(f) {
	if (f.elements["orderList"].selectedIndex == -1)
		return;
	
	removeSelected(f.elements["orderList"]);
}
