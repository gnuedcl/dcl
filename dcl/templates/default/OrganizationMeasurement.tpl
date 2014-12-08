<style>
	#sla-graph, #sla-daily-graph, #sla-distribution-graph { height: 320px; }

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
			{dcl_form_control}
				{dcl_select_measurement_type default={ViewData->MeasurementType} id=type}
			{/dcl_form_control}
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
		<h4 data-bind="text: type">Organization Measurement Report</h4>
		<h4><a href="{dcl_url_action controller=Organization action=Detail params="org_id={Org->org_id}"}" data-bind="text: orgName"></a> <small data-bind="text: periodName"></small></h4>
	</div>
</div>
<div class="row" data-bind="if: nonCompliantMeasurements().length > 0">
	<div class="col-sm-12">
		<div class="alert alert-danger"><!-- ko text: nonCompliantMeasurements().length --><!-- /ko --> measurement<!-- ko if: nonCompliantMeasurements().length == 1 --> is<!-- /ko --><!-- ko if: nonCompliantMeasurements().length > 1 -->s are<!-- /ko --> outside of the SLA threshold of <!-- ko text: slaThreshold --><!-- /ko --> <!-- ko text: unitAbbr --><!-- /ko -->.</div>
	</div>
</div>
<div class="row" data-bind="if: warningMeasurements().length > 0">
	<div class="col-sm-12">
		<div class="alert alert-warning"><!-- ko text: warningMeasurements().length --><!-- /ko --> measurement<!-- ko if: warningMeasurements().length == 1 --> is<!-- /ko --><!-- ko if: warningMeasurements().length > 1 -->s are<!-- /ko --> over the warning SLA of <!-- ko text: slaWarnThreshold --><!-- /ko --> <!-- ko text: unitAbbr --><!-- /ko --> and close to the SLA threshold of <!-- ko text: slaThreshold --><!-- /ko --> <!-- ko text: unitAbbr --><!-- /ko -->.</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<h4>All Measurements</h4>
		<div id="sla-graph"></div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<h4>Daily Measurements</h4>
		<div id="sla-daily-graph"></div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<h4>Daily Measurement Distribution (<span data-bind="if: slaIsTrim()"><!-- ko text: slaTrimPct --><!-- /ko -->% Trimmed Average</span><span data-bind="ifnot: slaIsTrim()">Average</span>)</h4>
		<div id="sla-distribution-graph"></div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<h4>Daily Measurement Details</h4>
		<table id="measurements" class="table table-condensed">
			<thead><tr><th>Date</th><th>Avg</th><th>Median</th><th>5% Trim Avg</th></thead>
			<tbody data-bind="foreach: measurements">
			<tr data-bind="attr: { class: className }"><td data-bind="text: date"></td><td data-bind="text: avg"></td><td data-bind="text: median"></td><td data-bind="text: trim"></td></tr>
			</tbody>
		</table>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<ul class="list-group">
				<li class="list-group-item">All measurements are in <!-- ko text: unitText -->?<!-- /ko --></li>
				<li class="list-group-item">Avg is the average daily measurement</li>
				<li class="list-group-item">Median is the median daily measurement</li>
				<li class="list-group-item"><!-- ko text: slaTrimPct --><!-- /ko -->% Trim Avg is the average with the top <!-- ko text: slaTrimPct --><!-- /ko -->% and bottom <!-- ko text: slaTrimPct --><!-- /ko -->% measurements removed</li>
			</ul>
		</div>
	</div>
</div>
<script src="{$DIR_VENDOR}flot/jquery.flot.min.js"></script>
<script src="{$DIR_VENDOR}flot/jquery.flot.time.min.js"></script>
<script src="{$DIR_VENDOR}flot/jquery.flot.categories.min.js"></script>
<script src="{$DIR_VENDOR}moment/moment.min.js"></script>
<script src="{$DIR_VENDOR}knockout/knockout-3.1.0.js"></script>
<script src="{$DIR_VENDOR}blockui/jquery.blockUI.js"></script>
<script type="text/javascript">
	function ViewModel() {
		var self = this;

		self.orgName = ko.observable("");
		self.periodName = ko.observable("");
		self.measurements = ko.observableArray([]);
		self.slaIsTrim = ko.observable(true);
		self.slaTrimPct = ko.observable(5);
		self.type = ko.observable("");

		self.unitName = ko.observable("");
		self.unitAbbr = ko.observable("");
		self.minValid = ko.observable(0);
		self.maxValid = ko.observable(0);
		self.slaThreshold = ko.observable(0);
		self.slaWarnThreshold = ko.observable(0);

		self.nonCompliantMeasurements = ko.observable([]);
		self.warningMeasurements = ko.observable([]);
		self.schedule = ko.observable([]);
		self.scheduleExceptions = ko.observable({});

		self.unitText = ko.computed(function() {
			return self.unitName() + " (" + self.unitAbbr() + ")";
		});
	}

	var viewModel = new ViewModel();

	$(function() {
		$("input[data-input-type=date]").datepicker();
		var $tooltip = $("#tooltip");
		var $sidebar = $("#sidebar");
		var $content = $("#content");


		ko.applyBindings(viewModel);

		var lastYear = 0, lastMonth = 0, lastDay = 0;
		var msSlice = [];
		var d = [], src = [];
		var avg = [], med = [], trm = [];
		var histogram = [ [ "0-999", 0 ], [ "1000-1999", 0 ], [ "2000-2999", 0 ], [ "3000-3999", 0 ], [ "4000-4999", 0 ], [ "5000-5999", 0 ], [ "6000-69999", 0 ], [ "7000+", 0 ] ];
		var measureSummary = [];
		var nonCompliantMeasurements = [], warningMeasurements = [];

		function updateView() {
			var params = {
				org_id: {Org->org_id},
				begin: $("#begin").val(),
				end: $("#end").val(),
				type: $("#type").val()
			};

			if (params.type == "")
			{
				$.gritter.add({ title: "Error", text: "Please select a measurement type for the report." });
				return;
			}

			$.blockUI.defaults.css.border = "none";
			$.blockUI.defaults.css.padding = "15px";
			$.blockUI.defaults.css.backgroundColor = "#000";
			$.blockUI.defaults.css.borderRadius = "10px";
			$.blockUI.defaults.css.color = "#fff";

			$("#content").block({ message: "<h4>Requesting measurements...</h4>" });
			$.getJSON("{$URL_MAIN_PHP}?menuAction=OrganizationMeasurementService.GetData",
				params,
				function (data) {
					viewModel.orgName(data.orgName);
					viewModel.periodName(data.periodName);
					viewModel.slaIsTrim(data.slaIsTrim);
					viewModel.slaTrimPct(data.slaTrimPct);
					viewModel.type(data.type);

					viewModel.unitAbbr(data.unitAbbr);
					viewModel.unitName(data.unitName);
					viewModel.minValid(data.minValid);
					viewModel.maxValid(data.maxValid);
					viewModel.slaThreshold(data.slaThreshold);

					viewModel.slaWarnThreshold(data.slaWarnThreshold);
					viewModel.schedule(data.schedule);
					viewModel.scheduleExceptions(data.scheduleExceptions);

					updateMeasurements(data.measurements);

					viewModel.nonCompliantMeasurements(nonCompliantMeasurements);
					viewModel.warningMeasurements(warningMeasurements);
				}
			).always(function() {
				$("#content").unblock();
			});
		}

		updateView();

		$content.on("click", "#update", updateView);

		function addToHistogram(value) {
			var bucket = Math.floor(value / 1000);
			if (bucket > 6)
				bucket = 7;

			histogram[bucket][1]++;
		}

		function calculateSliceMetrics(y, m, d) {
			var dt = new Date(y, m - 1, d);
			var ts = dt.getTime();
			msSlice.sort(function(a, b) { return a - b; });

			var sum = 0;
			msSlice.forEach(function(val) {
				sum += val;
			});

			var avgMs = Math.floor((sum / msSlice.length) + 0.5);
			var medMs = Math.floor(median(msSlice) + 0.5);

			avg.push([ ts, avgMs ]);
			med.push([ ts, medMs ]);

			var trmCount = Math.ceil(msSlice.length * (viewModel.slaTrimPct() / 100));
			if (trmCount * 2 < msSlice.length + 1) {
				msSlice = msSlice.slice(trmCount, msSlice.length - trmCount);

				sum = 0;
				msSlice.forEach(function(val) {
					sum += val;
				});
			}

			var trimMs = Math.floor((sum / msSlice.length) + 0.5);
			trm.push([ ts, trimMs ]);

			var measurement = { "date": moment(dt).format("L"), "avg": avgMs, "median": medMs, "trim": trimMs, "className": "" };
			var slaMeasurement = viewModel.slaIsTrim() ? trimMs : avgMs;
			if (viewModel.slaThreshold() != null && slaMeasurement > viewModel.slaThreshold()) {
				measurement.className = "bg-danger";
				nonCompliantMeasurements.push(measurement);
			} else if (viewModel.slaWarnThreshold() != null && slaMeasurement > viewModel.slaWarnThreshold()) {
				measurement.className = "bg-warning";
				warningMeasurements.push(measurement);
			}

			measureSummary.push(measurement);

			addToHistogram(slaMeasurement);
		}

		function median(values) {
			var half = Math.floor(values.length / 2);

			if (values.length % 2) {
				return values[half];
			}

			return (values[half - 1] + values[half]) / 2.0;
		}

		function isWithinSchedule(ts) {
			var dayOfWeek = ts.day();

			var schedule = viewModel.schedule();
			if (schedule.length == 7) {
				// If a schedule is provided, follow it
				var scheduleInfo = schedule[dayOfWeek];
				var startDt = moment(ts).hour(scheduleInfo.st.hr).minute(scheduleInfo.st.min);
				var endDt = moment(ts).hour(scheduleInfo.end.hr).minute(scheduleInfo.end.min);

				if (ts.isBefore(startDt, "minute") || ts.isAfter(endDt, "minute")) {
					return false;
				}
			}

			var scheduleExceptions = viewModel.scheduleExceptions();
			var exceptionKey = ts.format("YYYY-MM-DD");
			if (scheduleExceptions.hasOwnProperty(exceptionKey)) {
				// If we have exception time, ignore measurement if it's not in the window or the day is blocked off (start is null)
				var exception = scheduleExceptions[exceptionKey];
				if (exception.st == null) {
					return false;
				}

				var exceptionStartDt = moment(ts).hour(exception.st.hr).minute(exception.st.min);
				if (ts.isBefore(exceptionStartDt, "minute")) {
					return false;
				}

				var exceptionEndDt = moment(ts).hour(exception.end.hr).minute(exception.end.min);
				if (ts.isBefore(exceptionEndDt, "minute")) {
					return false;
				}
			}

			return true;
		}

		function isValidMeasurement(measure) {
			if ((viewModel.minValid() != null && measure.ms < viewModel.minValid()) || (viewModel.maxValid() != null && measure.ms >= viewModel.maxValid())) {
				// Invalid measurements (due to javascript error or something else...)
				return false;
			}

			if (!isWithinSchedule(moment(measure.ts))) {
				return false;
			}

			return true;
		}

		function updateMeasurements(src) {
			lastYear = 0;
			lastMonth = 0;
			lastDay = 0;
			msSlice = [];
			d = [];
			avg = [];
			med = [];
			trm = [];
			measureSummary = [];
			histogram = [ [ "0-999", 0 ], [ "1000-1999", 0 ], [ "2000-2999", 0 ], [ "3000-3999", 0 ], [ "4000-4999", 0 ], [ "5000-5999", 0 ], [ "6000-69999", 0 ], [ "7000+", 0 ] ];

			if (src.length > 0) {
				src.forEach(function (measure) {
					if (!isValidMeasurement(measure)) {
						// Invalid measurements (due to javascript error or something else...)
						return;
					}

					var dt = new Date(measure.ts);
					d.push([dt.getTime(), measure.ms]);

					if (lastYear == 0 || lastYear != dt.getFullYear() || lastMonth != (dt.getMonth() + 1) || lastDay != dt.getDate()) {
						if (lastYear != 0) {
							calculateSliceMetrics(lastYear, lastMonth, lastDay);

							msSlice = [];
						}

						lastYear = dt.getFullYear();
						lastMonth = dt.getMonth() + 1;
						lastDay = dt.getDate();
					}

					msSlice.push(measure.ms);
				});

				calculateSliceMetrics(lastYear, lastMonth, lastDay);
			}

			viewModel.measurements(measureSummary);

			var gridMarkings = [];
			if (viewModel.slaThreshold() != null) {
				gridMarkings.push({ color: "#0000a0", yaxis: { from: viewModel.slaThreshold(), to: viewModel.slaThreshold() }, lineWidth: 2 });
			}

			if (viewModel.slaWarnThreshold() != null) {
				gridMarkings.push({ color: "#ff9f00", yaxis: { from: viewModel.slaWarnThreshold(), to: viewModel.slaWarnThreshold() }, lineWidth: 2 });
			}

			$.plot("#sla-graph", [d], {
				xaxis: {
					mode: "time",
					timeformat: "%m/%d %H:%M",
					timezone: "browser"
				},
				grid: {
					markings: gridMarkings
				}
			});

			$.plot("#sla-daily-graph", [{ "label": "Average", data: avg }, { "label": "Median", data: med }, { "label": viewModel.slaTrimPct() + "% Trimmed Avg", data: trm }], {
				xaxis: {
					mode: "time",
					timeformat: "%m/%d/%Y",
					timezone: "browser",
					minTickSize: [ 1, "day" ]
				},
				series: {
					lines: { show: true },
					points: { show: true }
				},
				grid: {
					hoverable: true,
					clickable: true,
					markings: gridMarkings
				}
			});

			$.plot("#sla-distribution-graph", [ histogram ], {
				series: {
					bars: {
						show: true,
						barwidth: 0.6,
						align: "center"
					}
				},
				xaxis: {
					mode: "categories",
					tickLength: 0
				},
				yaxis: {
					min: 0
				}
			});
		}

		function getTooltipTop(pos) {
			return pos.pageY + $content.scrollTop() - $content.position().top;
		}

		function getTooltipLeft(pos) {
			return pos.pageX - $sidebar.width() - ($tooltip.width() / 2);
		}

		var seriesDescription = ["Average", "Median", "Trimmed Average"];
		function getMeasurementHoverDescription(item) {
			var dt = moment(item.datapoint[0]).format("ddd, MMM D, YYYY");
			var measurement = item.datapoint[1];

			return seriesDescription[item.seriesIndex] + " on<br/>" + dt + "<br/>" + measurement + " " + viewModel.unitAbbr();
		}

		function measurementHover(event, pos, item) {
			if (item) {
				$tooltip.html(getMeasurementHoverDescription(item))
						.css({ top: getTooltipTop(pos), left: getTooltipLeft(pos) })
						.fadeIn(200);
			} else {
				$tooltip.hide();
			}
		}

		$("#sla-daily-graph").bind("plothover", measurementHover);
	});
</script>
