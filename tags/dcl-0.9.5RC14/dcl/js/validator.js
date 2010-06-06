function Validator()
{
	var _Element = null;
	var _Title = "";
	var aErrors = new Array();

	this.init = function()
	{
		this.aErrors = new Array();
		this.validate();
	}

	this.setError = function(sMsg)
	{
		this.aErrors[this.aErrors.length] = sMsg;
	}

	this.isValid = function()
	{
		return (this.aErrors.length == 0);
	}

	this.getError = function()
	{
		if (this.aErrors.length == 0)
			return "";

		return this.aErrors[0];
	}

	this.getElement = function()
	{
		return this._Element;
	}
}

function ValidatorSelection(oElement, sTitle)
{
	this.validate = function()
	{
		if (oElement && oElement.type == 'select-one' && oElement.options.length > 1 && oElement.selectedIndex < 1)
			this.setError(this._Title + " is required.");
	}

	this._Element = oElement;
	this._Title = sTitle;
	this.init();
}

function ValidatorString(oElement, sTitle)
{
	this.validate = function()
	{
		if (!oElement)
			return;
		
		var i = oElement.value.length - 1;
		while (i >= 0 && oElement.value[i] == " ")
			i--;
			
		if (i != oElement.value.length - 1)
			oElement.value = oElement.value.substring(0, i + 1);
			
		if (oElement.value.length < 1)
			this.setError(this._Title + " is required.");
	}

	this._Element = oElement;
	this._Title = sTitle;
	this.init();
}

function ValidatorEmail(oElement, sTitle, bRequired)
{
	this.validate = function()
	{
		if (!oElement)
			return;

		if (oElement.value == "")
		{
			if (bRequired)
				this.setError(this._Title + " is required.");
				
			return;
		}
		
		var oRegEx = new RegExp("^\\w+([\\.-]?\\w+)*@\\w+([\\.-]?\\w+)*(\\.\\w{2,})+$");
		if (!oRegEx.test(oElement.value))
		{
			this.setError(this._Title + " does not contain a valid E-Mail address.");
		}
	}

	this._Element = oElement;
	this._Title = sTitle;
	this.init();
}

function ValidatorInteger(oElement, sTitle, bRequired)
{
	this.validate = function()
	{
		if (!oElement)
			return;

		if (oElement.value == "")
		{
			if (bRequired)
				this.setError(this._Title + " is required.");
				
			return;
		}
		
		var oRegEx = new RegExp("^\\d+$");
		if (!oRegEx.test(oElement.value))
		{
			this.setError(this._Title + " does not contain a valid number.");
		}
	}

	this._Element = oElement;
	this._Title = sTitle;
	this.init();
}

function ValidatorSelector(oElement, sTitle)
{
	this.validate = function()
	{
		if (!oElement)
			return;
			
		if (oElement.value == "")
		{
			this.setError(this._Title + " is required.");
			return;
		}

		var oRegEx = new RegExp("^\\d+(,\\d+)*$");
		if (!oRegEx.test(oElement.value))
		{
			this.setError(this._Title + " does not contain a valid number.");
			return;
		}
		
		if (oElement.value < 1)
		{
			this.setError(this._Title + " is required.");
		}
	}

	this._Element = oElement;
	this._Title = sTitle;
	this.init();
}

function ValidatorDecimal(oElement, sTitle, bRequired)
{
	this.validate = function()
	{
		if (!oElement)
			return;

		if (oElement.value == "")
		{
			if (bRequired)
				this.setError(this._Title + " is required.");
				
			return;
		}
		
		var oRegEx = new RegExp("^\\d*[\\.]?\\d*$");
		if (!oRegEx.test(oElement.value))
		{
			this.setError(this._Title + " does not contain a valid decimal.");
			return;
		}

		var fNum = parseFloat(oElement.value);
		if (isNaN(fNum))
		{
			this.setError(this._Title + " does not contain a valid decimal.");
		}
	}

	this._Element = oElement;
	this._Title = sTitle;
	this.init();
}

function ValidatorDate(oElement, sTitle, bRequired)
{
	this.validate = function()
	{
		if (!oElement)
			return;

		if (oElement.value == "")
		{
			if (bRequired)
				this.setError(this._Title + " is required.");
				
			return;
		}
		
		var sValue = oElement.value;
		var iYear  = parseInt(sValue.substr(calDateFormat.indexOf("y"), 4), 10);
		var iMonth = parseInt(sValue.substr(calDateFormat.indexOf("mm"), 2), 10);
		var iDay   = parseInt(sValue.substr(calDateFormat.indexOf("dd"), 2), 10);

		if (!isNaN(iYear) && !isNaN(iMonth) && !isNaN(iDay))
		{
			if (iYear >= 0 && iYear < 100)
			{
				if (iYear < 80)
					iYear += 2000;
				else
					iYear += 1900;
			}
			else if (iYear < 1000 || iYear > 9999)
			{
				this.setError(this._Title + " does not contain a valid date.");
				return;
			}
		}
		else
		{
			this.setError(this._Title + " does not contain a valid date.");
			return;
		}

		if (iMonth < 1 || iMonth > 12 || iDay < 1 || iDay > 31)
		{
			this.setError(this._Title + " does not contain a valid date.");
			return;
		}

		var oDate = new Date(iMonth + "/" + iDay + "/" + iYear);
		if ((oDate.getMonth() + 1) != iMonth)
			this.setError(this._Title + " does not contain a valid date.");
	}

	this._Element = oElement;
	this._Title = sTitle;
	this.init();
}

ValidatorEmail.prototype = new Validator();
ValidatorEmail.prototype.constructor = ValidatorEmail;

ValidatorInteger.prototype = new Validator();
ValidatorInteger.prototype.constructor = ValidatorInteger;

ValidatorSelector.prototype = new Validator();
ValidatorSelector.prototype.constructor = ValidatorSelector;

ValidatorString.prototype = new Validator();
ValidatorString.prototype.constructor = ValidatorString;

ValidatorDecimal.prototype = new Validator();
ValidatorDecimal.prototype.constructor = ValidatorDecimal;

ValidatorDate.prototype = new Validator();
ValidatorDate.prototype.constructor = ValidatorDate;

ValidatorSelection.prototype = new Validator();
ValidatorSelection.prototype.constructor = ValidatorSelection;