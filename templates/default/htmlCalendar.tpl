<!-- $Id: htmlCalendar.tpl,v 1.1.1.1 2006/11/27 05:30:37 mdean Exp $ -->
<link rel="stylesheet" type="text/css" media="all" href="calendar/calendar-system.css" title="system" />
<script type="text/javascript" src="calendar/calendar.js"></script>
<script type="text/javascript" src="calendar/lang/calendar-en.js"></script>
<script language="JavaScript">
	function _dateSelected(cal, date)
	{
		cal.sel.value = date;
		
		if (cal.dateClicked)
			cal.callCloseHandler();
	}

	function _closeHandler(cal)
	{
		cal.hide();
	}

	function showCalendar(id)
	{
		var el = document.getElementById(id);
		if (_dynarch_popupCalendar != null)
		{
			_dynarch_popupCalendar.hide();
		}
		else
		{
			var cal = new Calendar(false, null, _dateSelected, _closeHandler);
			cal.weekNumbers = false;
			_dynarch_popupCalendar = cal;
			cal.setRange(1900, 2070);
			cal.create();
		}

		_dynarch_popupCalendar.setDateFormat('{VAL_JSDATEFORMAT}');
		_dynarch_popupCalendar.parseDate(el.value);
		_dynarch_popupCalendar.sel = el;
		_dynarch_popupCalendar.showAtElement(el);

		return false;
	}
</script>
