<style>
	#sla-uptime-unplanned, #sla-uptime, #sla-outages { height: 320px; }

	#tooltip {
		position: absolute;
		display : none;
		padding : 4px;
		background-color : #111;
		border-radius: 4px;
		opacity : 0.80;
		color: #fff;
		z-index: 1000;
		text-align: center;
	}
</style>
<div id="tooltip" class="noprint"></div>
<div class="row noprint">
	<div class="col-sm-12">
		<form class="form-inline" role="form">
			{dcl_form_control id=begin}
				<input type="text" placeholder="Begin" class="form-control" data-input-type="date" maxlength="10" id="begin" name="begin" value="{ViewData->BeginDate|escape}">
			{/dcl_form_control}
			{dcl_form_control label="to"}{/dcl_form_control}
			{dcl_form_control id=end}
				<input type="text" placeholder="End" class="form-control" data-input-type="date" maxlength="10" id="end" name="end" value="{ViewData->EndDate|escape}">
			{/dcl_form_control}
			<button type="button" class="btn btn-default" id="update">Update</button>
		</form>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<h4>Organization Outage Report</h4>
		<h4><a href="{dcl_url_action controller=Organization action=Detail params="org_id={Org->org_id}"}" data-bind="text: orgName"></a> <small data-bind="text: periodName"></small></h4>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<p>Uptime For This Period: <span data-bind="text: uptimePercentage"></span>%
			<!-- ko if: isSlaWarning --><span class="text-warning"><span class="glyphicon glyphicon-warning-sign text-warning"></span> The uptime is below the warning threshold of <!-- ko text: slaWarnThreshold --><!-- /ko -->%.  The compliance threshold is <!-- ko text: slaThreshold --><!-- /ko -->%.</span><!-- /ko -->
			<!-- ko if: isSlaNoncompliance --><span class="text-danger"><span class="glyphicon glyphicon-exclamation-sign text-danger"></span>  The uptime is below the compliance threshold of <!-- ko text: slaThreshold --><!-- /ko -->%.</span><!-- /ko -->
		</p>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<h4>Uptime <small>Unplanned Downtime Only</small></h4>
		<div id="sla-uptime-unplanned"></div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<h4>Total Uptime <small>Planned and Unplanned Downtime</small></h4>
		<div id="sla-uptime"></div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<h4>Outages <small>May Include Overlapping Outages</small></h4>
		<div id="sla-outages"></div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<h4>Outage Details</h4>
		<table id="measurements" class="table table-striped table-condensed">
			<thead><tr><th>Date/Time</th><th>Status</th><th>Type</th><th>Title</th><th>Duration</th></thead>
			<tbody data-bind="foreach: outages">
			<tr><td><span data-bind="attr: { class: getIcon }"></span> <!-- ko text: startDisplay --><!-- /ko --> - <!-- ko text: endDisplay --><!-- /ko --></td><td data-bind="text: statusName"></td><td data-bind="text: typeName"></td><td data-bind="text: title"></td><td data-bind="text: duration"></td></tr>
			</tbody>
		</table>
	</div>
</div>
<script src="{$DIR_VENDOR}lodash/lodash.min.js"></script>
<script src="{$DIR_VENDOR}flot/jquery.flot.min.js"></script>
<script src="{$DIR_VENDOR}flot/jquery.flot.time.min.js"></script>
<script src="{$DIR_VENDOR}flot/jquery.flot.categories.min.js"></script>
<script src="{$DIR_VENDOR}flot/jquery.flot.threshold.min.js"></script>
<script src="{$DIR_VENDOR}moment/moment.min.js"></script>
<script src="{$DIR_VENDOR}knockout/knockout-3.2.0.js"></script>
<script src="{$DIR_VENDOR}blockui/jquery.blockUI.js"></script>
<script type="text/javascript">
function ViewModel() {
	var self = this;

	self.orgName = ko.observable("");
	self.periodName = ko.observable("");
	self.outages = ko.observableArray([]);

	self.slaThreshold = ko.observable(0);
	self.slaWarnThreshold = ko.observable(0);

	self.totalMinutes = ko.observable(1);
	self.downMinutes = ko.observable(0);

	self.uptimePercentage = ko.computed(function() {
		if (self.downMinutes() == 0 || self.totalMinutes() == 0) {
			return 100.0;
		}

		return 100.0 - (self.downMinutes() / self.totalMinutes() * 100);
	});

	self.isSlaNoncompliance = ko.computed(function() {
		var pct = self.uptimePercentage();
		return pct < self.slaThreshold();
	});

	self.isSlaWarning = ko.computed(function () {
		var pct = self.uptimePercentage();
		return !self.isSlaNoncompliance() && pct < self.slaWarnThreshold();
	});
}

function DateUptimeModel(day) {
	var self = this;

	self.dt = moment(day);
	self.availableMinutes = 1440;
	self.downtimeMinutes = 0;
	self.plannedDowntimeMinutes = 0
	self.unplannedDowntimeMinutes = 0;
	self.uptimeMinutes = 0;
	self.unplannedUptimeMinutes = 0;
	self.plannedUptimeMinutes = 0;
	self.outageWindows = [];
	self.flattenedOutages = [];
	self.outageEvents = [];

	self.getUptime = function() {
		return 100.0 - ((self.downtimeMinutes / self.availableMinutes) * 100);
	};

	self.getUptimeUnplannedOnly = function() {
		return 100.0 - ((self.unplannedDowntimeMinutes / self.availableMinutes) * 100);
	};

	self.getOutageRank = function(isDown, isPlanned) {
		var rank = 1;
		if (!isDown)
			rank += 2;

		if (isPlanned)
			rank++;

		return rank;
	};

	self.findOverlap = function(outages, startDt, endDt) {
		return _.filter(outages, function(item) {
			if (item.startDt.isAfter(endDt, "minute") || item.endDt.isBefore(startDt, "minute")) {
				return false;
			}

			return true;
		});
	};

	self.addOutage = function(id, startDt, endDt, isDown, isPlanned) {
		var outageRank = self.getOutageRank(isDown, isPlanned);
		self.outageWindows[id] = {
			id: id,
			startDt: startDt,
			endDt: endDt,
			isDown: isDown,
			isPlanned: isPlanned,
			rank: outageRank
		};

		self.outageEvents.push({
			id: id,
			isEnd: false,
			rank: outageRank,
			dt: startDt,
			ticks: startDt.valueOf()
		});

		self.outageEvents.push({
			id: id,
			isEnd: true,
			rank: outageRank,
			dt: endDt,
			ticks: endDt.valueOf()
		});
	};

	self.addMinutes = function(startDt, endDt, isDown, isPlanned) {
		var minutes = endDt.diff(startDt, "minutes");
		if (endDt.seconds() > 29)
			minutes++;

		console.log({ s: startDt.format(), e: endDt.format(), m: minutes });
		if (isDown) {
			if (!isPlanned) {
				self.unplannedDowntimeMinutes += minutes;
			} else {
				self.plannedDowntimeMinutes += minutes;
			}
		} else {
			self.uptimeMinutes += minutes;
			if (!isPlanned) {
				self.unplannedUptimeMinutes += minutes;
			} else {
				self.plannedUptimeMinutes += minutes;
			}
		}
	}

	self.aggregateOutages = function() {
		var sortedOutageEvents = _.sortBy(self.outageEvents, "ticks");

		var outageStartStack = [];
		outageStartStack[1] = [];
		outageStartStack[2] = [];
		outageStartStack[3] = [];
		outageStartStack[4] = [];
		outageStartStack[5] = [];

		var outageEndStack = [];
		outageEndStack[1] = [];
		outageEndStack[2] = [];
		outageEndStack[3] = [];
		outageEndStack[4] = [];
		outageEndStack[5] = [];

		var stackItem;
		_.forEach(sortedOutageEvents, function(ev) {
			var startOutage, endOutage;

			if (!ev.isEnd) {
				outageStartStack[ev.rank].push(ev);
				if (ev.rank < 3) {
					outageStartStack[5].push(ev);
				}
			} else {
				outageEndStack[ev.rank].push(ev);
				if (ev.rank < 3) {
					outageEndStack[5].push(ev);
				}
			}

			if (outageStartStack[ev.rank].length == outageEndStack[ev.rank].length) {
				// If the stacks are even, we have matching (possibly overlapping) events all captured
				startOutage = _.min(outageStartStack[ev.rank], "dt");
				endOutage = _.max(outageEndStack[ev.rank], "dt");

				self.addMinutes(startOutage.dt, endOutage.dt, ev.rank < 3, ev.rank % 2 == 0);

				outageStartStack[ev.rank] = [];
				outageEndStack[ev.rank] = [];
			}

			if (outageStartStack[5].length > 0 && outageStartStack[5].length == outageEndStack[5].length) {
				// If the stacks are even, we have matching (possibly overlapping) events all captured
				startOutage = _.min(outageStartStack[5], "dt");
				endOutage = _.max(outageEndStack[5], "dt");

				self.downtimeMinutes += endOutage.dt.diff(startOutage.dt, "minutes");
				if (endOutage.dt.seconds > 29)
					self.downtimeMinutes++;

				outageStartStack[5] = [];
				outageEndStack[5] = [];
			}
		});
	};
}

var viewModel = new ViewModel();

$(function() {
	$("input[data-input-type=date]").datepicker();

	ko.applyBindings(viewModel);

	function updateView() {
		var params = {
			org_id: {Org->org_id},
			begin: $("#begin").val(),
			end: $("#end").val()
		};

		$.blockUI.defaults.css.border = "none";
		$.blockUI.defaults.css.padding = "15px";
		$.blockUI.defaults.css.backgroundColor = "#000";
		$.blockUI.defaults.css.borderRadius = "10px";
		$.blockUI.defaults.css.color = "#fff";

		$("#content").block({ message: "<h4>Requesting outages...</h4>" });
		$.getJSON("{$URL_MAIN_PHP}?menuAction=OrganizationOutageService.GetData",
			params,
			function (data) {
				viewModel.orgName(data.orgName);
				viewModel.periodName(data.periodName);
				viewModel.slaThreshold(data.slaThreshold);
				viewModel.slaWarnThreshold(data.slaWarnThreshold);
				updateOutages(data.outages);
				viewModel.outages(data.outages);
			}
		).always(function() {
			$("#content").unblock();
		});
	}

	updateView();

	$("#content").on("click", "#update", updateView);

	function appendTo(text, value, delim) {
		if (text != "")
			text += delim;

		return text + value;
	}

	function getUnitIfPresent(value, singularUnit, pluralUnit) {
		if (value > 1)
			return value + " " + pluralUnit;

		if (value > 0)
			return value + " " + singularUnit;

		return "";
	}

	function getDurationText(duration) {
		var years = duration.years();
		var months = duration.months();
		var days = duration.days();
		var hours = duration.hours();
		var minutes = duration.minutes();

		var text = "";
		text = appendTo(text, getUnitIfPresent(years, "Year", "Years"), " ");
		text = appendTo(text, getUnitIfPresent(months, "Month", "Months"), " ");
		text = appendTo(text, getUnitIfPresent(days, "Day", "Days"), " ");
		text = appendTo(text, getUnitIfPresent(hours, "Hour", "Hours"), " ");
		text = appendTo(text, getUnitIfPresent(minutes, "Minute", "Minutes"), " ");

		return text;
	}

	function updateOutages(src) {
		var startDt = moment($("#begin").val());
		var endDt = moment($("#end").val());
		var currentDt = moment(startDt);
		var unplannedOutageData = [];
		var downtimeOutageData = [];

		var unplannedDownMinutes = [];
		var plannedDownMinutes = [];
		var unplannedUpMinutes = [];
		var plannedUpMinutes = [];

		var allOutageData = [];
		while (!currentDt.isAfter(endDt, "day")) {
			allOutageData.push(new DateUptimeModel(currentDt));
			currentDt.add(1, "days");
		}

		if (src.length > 0) {
			src.forEach(function(outage) {
				var idx, daySlice, outageDistEndDt, durationMinutes, outageEndDt;
				var outageStartDt = moment(outage.start);

				if (outage.end == null) {
					if (endDt.isSame(moment(), "day")) {
						outageEndDt = moment();
					} else {
						outageEndDt = moment(endDt).endOf("day");
					}
				} else {
					outageEndDt = moment(outage.end);
				}

				// Calculate duration for detail prior to altering data for graphs
				durationMinutes = outageEndDt.diff(outageStartDt, "minutes");
				if (outageEndDt.seconds > 29)
					durationMinutes++;

				outage.duration = getDurationText(moment.duration(durationMinutes, "minutes"));
				outage.startDisplay = outageStartDt.format("L HH:mm");

				outage.getIcon = ko.computed(function() {
					var classNames = ["glyphicon"];

					if (outage.isDown == "Y") {
						if (outage.isPlanned == "Y") {
							classNames.push("glyphicon-info-sign");
							classNames.push("text-danger");
						}
						else {
							classNames.push("glyphicon-exclamation-sign");
							classNames.push("text-danger");
						}
					}
					else {
						if (outage.planned == "Y") {
							classNames.push("glyphicon-info-sign");
							classNames.push("text-success");
						}
						else {
							classNames.push("glyphicon-warning-sign");
							classNames.push("text-warning");
						}
					}

					return classNames.join(" ");
				});

				if (outage.end != null)
					outage.endDisplay = outageEndDt.format("L HH:mm");
				else
					outage.endDisplay = "Present";

				if (outageStartDt.isBefore(startDt, "day")) {
					outageStartDt = moment(startDt);
				}

				if (outageEndDt.isAfter(endDt, "day")) {
					outageEndDt = moment(endDt).endOf("day");
				}

				if (outageEndDt.isAfter(moment(), "minute")) {
					outageEndDt = moment();
				}

				if (outageStartDt.isSame(outageEndDt, "day")) {
					// Started and ended on this day (or was still going on at the end)
					idx = outageEndDt.diff(startDt, "days");
					daySlice = allOutageData[idx];

					daySlice.addOutage(outage.id, outageStartDt, outageEndDt, outage.isDown == "Y", outage.isPlanned == "Y");
				} else {
					// Spans multiple days, so we need to distribute the minutes across multiple days
					var outageDistStartDt = moment(outageStartDt);
					while (!outageDistStartDt.isAfter(outageEndDt, "day")) {
						idx = outageDistStartDt.diff(startDt, "days");
						daySlice = allOutageData[idx];

						outageDistEndDt = moment(outageDistStartDt).endOf("day");
						if (outageDistEndDt.isAfter(outageEndDt, "minute")) {
							outageDistEndDt = moment(outageEndDt);
						}

						daySlice.addOutage(outage.id, moment(outageDistStartDt), outageDistEndDt, outage.isDown == "Y", outage.isPlanned == "Y");
						outageDistStartDt.add(1, "days").startOf("day");
					}
				}
			});
		}

		var totalMinutes = 0, downMinutes = 0;
		$.each(allOutageData, function(k, v) {
			v.aggregateOutages();
			unplannedOutageData.push([v.dt, v.getUptimeUnplannedOnly()]);
			downtimeOutageData.push([v.dt, v.getUptime()]);
			unplannedDownMinutes.push([v.dt, v.unplannedDowntimeMinutes]);
			plannedDownMinutes.push([v.dt, v.plannedDowntimeMinutes]);
			unplannedUpMinutes.push([v.dt, v.unplannedUptimeMinutes]);
			plannedUpMinutes.push([v.dt, v.plannedUptimeMinutes]);

			totalMinutes += v.availableMinutes;
			downMinutes += v.downtimeMinutes;
		});

		viewModel.totalMinutes(totalMinutes);
		viewModel.downMinutes(downMinutes);

		$.plot("#sla-uptime-unplanned", [{
			data: unplannedOutageData,
			bars: {
				show: true,
				barWidth: 12 * 24 * 60 * 60 * allOutageData.length * 2,
				align: "center",
				fill: true
			},
			threshold: {
				below: viewModel.slaThreshold(),
				color: "rgb(200, 20, 30)"
			}
		}], {
			xaxis: {
				mode: "time",
				timeformat: "%m/%d/%Y",
				timezone: "browser",
				minTickSize: [1, "day"]
			},
			yaxis: {
				max: 100,
				min: 0
			},
			grid: {
				hoverable: true,
				clickable: true
			},
			colors: [
				"green"
			]
		});

		$.plot("#sla-uptime", [{
			data: downtimeOutageData,
			bars: {
				show: true,
				barWidth: 12 * 24 * 60 * 60 * allOutageData.length * 2,
				align: "center",
				fill: true
			}
		}], {
			xaxis: {
				mode: "time",
				timeformat: "%m/%d/%Y",
				timezone: "browser",
				minTickSize: [1, "day"]
			},
			yaxis: {
				max: 100,
				min: 0
			},
			grid: {
				hoverable: true,
				clickable: true
			},
			colors: [
				"#6495ed"
			]
		});

		$.plot("#sla-outages", [{
			data: unplannedDownMinutes,
			lines: {
				show: true
			},
			points: {
				show: true
			}
		}, {
			data: plannedDownMinutes,
			lines: {
				show: true
			},
			points: {
				show: true
			}
		}, {
			data: unplannedUpMinutes,
			lines: {
				show: true
			},
			points: {
				show: true
			}
		}, {
			data: plannedUpMinutes,
			lines: {
				show: true
			},
			points: {
				show: true
			}
		}], {
			xaxis: {
				mode: "time",
				timeformat: "%m/%d/%Y",
				timezone: "browser",
				minTickSize: [1, "day"]
			},
			yaxis: {
				min: 0
			},
			grid: {
				hoverable: true,
				clickable: true
			},
			colors: [
				"#a00000",
				"#ff7518",
				"#fdee00",
				"#6495ed"
			]
		});

		var $tooltip = $("#tooltip");
		var $sidebar = $("#sidebar");
		var $content = $("#content");

		function getTooltipTop(pos) {
			return pos.pageY + $content.scrollTop() - $content.position().top;
		}

		function getTooltipLeft(pos) {
			return pos.pageX - $sidebar.width() - ($tooltip.width() / 2);
		}

		function uptimeHover(event, pos, item) {
			if (item) {
				var dt = moment(item.datapoint[0]).format("ddd, MMM D, YYYY");
				var pct = item.datapoint[1].toFixed(2);

				$tooltip.html("Uptime on<br/>" + dt + "<br/>" + pct + "%")
						.css({ top: getTooltipTop(pos), left: getTooltipLeft(pos) })
						.fadeIn(200);
			} else {
				$tooltip.hide();
			}
		}

		var seriesDescription = ["Unplanned Downtime", "Planned Downtime", "Unplanned Uptime", "Planned Uptime"];
		function getMinutesHoverDescription(item) {
			var dt = moment(item.datapoint[0]).format("ddd, MMM D, YYYY");
			var minutes = item.datapoint[1];

			if (minutes == 0)
				return "No " + seriesDescription[item.seriesIndex];

			return seriesDescription[item.seriesIndex] + " on<br/>" + dt + "<br/>" + minutes + " min";
		}

		function minutesHover(event, pos, item) {
			if (item) {
				$tooltip.html(getMinutesHoverDescription(item))
						.css({ top: getTooltipTop(pos), left: getTooltipLeft(pos) })
						.fadeIn(200);
			} else {
				$tooltip.hide();
			}
		}

		$("#sla-uptime-unplanned,#sla-uptime").bind("plothover", uptimeHover);
		$("#sla-outages").bind("plothover", minutesHover);
	}
});
</script>