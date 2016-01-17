{extends file="_Layout.tpl"}
{block name=title}Dashboard{/block}
{block name=css}
	<style>
		#dashboard {
			margin-top: 10px;
		}
		.dcl-chart {
			height: 160px;
			margin-top: 10px;
		}

		.grid-panel h2 {
			margin-top: 1px;
		}

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

		.legendLabel {
			cursor: pointer;
		}
	</style>
{/block}
{block name=content}
<div id="tooltip" class="noprint"></div>
<div class="container">
	<div id="dashboard" class="row">
		{if $PERM_OUTAGES}
			<div class="col-xs-6">
				<div class="panel panel-default">
					<div class="panel-heading"><h3 class="panel-title">Outages</h3></div>
					<div class="panel-body">
						<div class="dcl-chart" id="outages_chart" data-ref="outages"></div>
					</div>
				</div>
			</div>
		{/if}
		{if $PERM_WORKORDERS}
			<div class="col-xs-6">
				<div class="panel panel-default">
					<div class="panel-heading"><h3 class="panel-title">Work Orders</h3></div>
					<div class="panel-body">
						<div class="dcl-chart" id="workorders_chart" data-ref="workOrders"></div>
					</div>
				</div>
			</div>
		{/if}
		{if $PERM_TICKETS}
			<div class="col-xs-6">
				<div class="panel panel-default">
					<div class="panel-heading"><h3 class="panel-title">Tickets</h3></div>
					<div class="panel-body">
						<div class="dcl-chart" id="tickets_chart" data-ref="tickets"></div>
					</div>
				</div>
			</div>
		{/if}
		{if $PERM_PROJECTS}
			<div class="col-xs-6">
				<div class="panel panel-default">
					<div class="panel-heading"><h3 class="panel-title">Projects</h3></div>
					<div class="panel-body">
						<div class="dcl-chart" id="projects_chart" data-ref="projects"></div>
					</div>
				</div>
			</div>
		{/if}
	</div>
</div>
{if $PERM_OUTAGES}
<div id="currentOutages" style="display: none;" data-bind="if: outages().length">
	<div class="panel" data-bind="attr: { class: outageClass }">
		<div class="panel-heading"><h3 class="panel-title">Current Outages</h3></div>
		<ul class="list-group" data-bind="foreach: outages">
			<li class="list-group-item">
				<span data-bind="attr: { class: getIcon }"></span>
				<a href="javascript:;" data-bind="text: title"></a>
				<!-- ko text: status --><!-- /ko --> <span class="text-muted">(Started <!-- ko text: start --><!-- /ko --><!-- ko if: planned === 'Y' -->, Scheduled for <!-- ko text: schedstart --><!-- /ko --> to <!-- ko text: schedend --><!-- /ko --><!-- /ko -->)</span>
			</li>
		</ul>
	</div>
</div>
<div id="upcomingOutages" style="display: none;" data-bind="if: outages().length">
	<div class="panel panel-info">
		<div class="panel-heading"><h3 class="panel-title">Upcoming Planned Outages</h3></div>
		<ul class="list-group" data-bind="foreach: outages">
			<li class="list-group-item">
				<span data-bind="attr: { class: getIcon }"></span>
				<a href="javascript:;" data-bind="text: title"></a>
				<span class="text-muted"><!-- ko text: schedstart --><!-- /ko --> &mdash; <!-- ko text: schedend --><!-- /ko --></span>
			</li>
		</ul>
	</div>
</div>
<div id="recentOutages" style="display: none;" data-bind="if: outages().length">
	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title">Recent Outages</h3></div>
		<ul class="list-group" data-bind="foreach: outages">
			<li class="list-group-item">
				<span data-bind="attr: { class: getIcon }"></span>
				<a href="javascript:;" data-bind="text: title"></a>
				<!-- ko text: status --><!-- /ko --> <span class="text-muted">(<!-- ko text: start --><!-- /ko --> to <!-- ko text: end --><!-- /ko --><!-- ko if: planned === 'Y' -->, Scheduled for <!-- ko text: schedstart --><!-- /ko --> to <!-- ko text: schedend --><!-- /ko --><!-- /ko -->)</span>
			</li>
		</ul>
	</div>
</div>
{/if}
{/block}
{block name=script}
<script type="text/javascript" src="{$DIR_VENDOR}knockout/knockout-3.3.0.js"></script>
<script src="{$DIR_VENDOR}flot/jquery.flot.min.js"></script>
<script src="{$DIR_VENDOR}flot/jquery.flot.time.min.js"></script>
<script src="{$DIR_VENDOR}flot/jquery.flot.categories.min.js"></script>
<script src="{$DIR_VENDOR}flot/jquery.flot.pie.min.js"></script>
<script type="text/javascript">
	$(function() {
		{if $PERM_OUTAGES}
		var currentOutageViewModel = {
			outages: ko.observableArray([]),
			outageClass: ko.observable("")
		};

		ko.applyBindings(currentOutageViewModel, document.getElementById("currentOutages"));

		var upcomingOutageViewModel = {
			outages: ko.observableArray([])
		};

		ko.applyBindings(upcomingOutageViewModel, document.getElementById("upcomingOutages"));

		var recentOutageViewModel = {
			outages: ko.observableArray([])
		};

		ko.applyBindings(recentOutageViewModel, document.getElementById("recentOutages"));
		{/if}

		var $tooltip = $("#tooltip");
		var $sidebar = $("#sidebar");
		var $content = $("#content");

		var pieData = {
			workOrders: [],
			tickets: [],
			projects: [],
			outages: []
		}

		var pieOptions = {
			series: {
				pie: {
					radius: 1,
					innerRadius: 0.5,
					show: true
				}
			},
			grid: {
				hoverable: true,
				clickable: true
			},
			colors: [
				"#43658b",
				"#732800",
				"#a88300"
			]
		};

		function getTooltipTop(pos) {
			return pos.pageY + $content.scrollTop() - $content.position().top;
		}

		function getTooltipLeft(pos) {
			return pos.pageX - $sidebar.width() - ($tooltip.width() / 2);
		}

		function getHoverDescription(series, item) {
			if (item == null)
				return "";

			return series[item.seriesIndex].label;
		}

		function doHover(text, pos, item) {
			if (item) {
				$tooltip.html(text)
						.css({ top: getTooltipTop(pos), left: getTooltipLeft(pos) })
						.fadeIn(200);
			} else {
				$tooltip.hide();
			}
		}

		var urlMainPhp = "{$URL_MAIN_PHP}";

		{if $PERM_OUTAGES}
		var outagePanelClasses = ["panel-danger", "panel-warning", "panel-success"];
		var outagePanelClass = 2;

		function updateCurrentOutages() {
			return $.ajax({
				type: "POST",
				url: urlMainPhp,
				data: { menuAction: "OutageService.GetCurrentOutages" },
				dataType: "json"
			}).done(function(data) {
				outagePanelClass = 2;

				$.each(data.outages, function(k, v) {
					v.getIcon = ko.computed(function() {
						var classNames = ["glyphicon"];

						if (v.down == "Y") {
							if (v.planned == "Y") {
								classNames.push("glyphicon-info-sign");
								classNames.push("text-danger");
							}
							else {
								classNames.push("glyphicon-exclamation-sign");
								classNames.push("text-danger");
							}

							outagePanelClass = 0;
						}
						else {
							if (v.planned == "Y") {
								classNames.push("glyphicon-info-sign");
								classNames.push("text-success");
							}
							else {
								classNames.push("glyphicon-warning-sign");
								classNames.push("text-warning");

								if (outagePanelClass > 1)
									outagePanelClass = 1;
							}
						}

						return classNames.join(" ");
					});
				});

				currentOutageViewModel.outageClass(outagePanelClasses[outagePanelClass]);
				currentOutageViewModel.outages(data.outages);
				pieData.outages[0] = { label: "Current Outages: " + data.outages.length, data: data.outages.length, href: "#currentOutages" };

				if (data.outages.length > 0) {
					$("#currentOutages").show('fade');
				}
			}).error(function() {
				$.gritter.add({ title: "Error", text: "Could not read current outages." });
			});
		}

		function updateUpcomingOutages() {
			return $.ajax({
				type: "POST",
				url: urlMainPhp,
				data: { menuAction: "OutageService.GetUpcomingPlannedOutages" },
				dataType: "json"
			}).done(function(data) {
				$.each(data.outages, function(k, v) {
					v.getIcon = ko.computed(function() {
						var classNames = ["glyphicon", "text-info"];

						if (v.down == "Y")
							classNames.push("glyphicon-info-sign");
						else
							classNames.push("glyphicon-warning-sign");

						return classNames.join(" ");
					});
				});

				upcomingOutageViewModel.outages(data.outages);
				pieData.outages[1] = { label: "Upcoming Outages: " + data.outages.length, data: data.outages.length, href: "#upcomingOutages" };

				if (data.outages.length > 0) {
					$("#upcomingOutages").show('fade');
				}
			}).error(function() {
				$.gritter.add({ title: "Error", text: "Could not read upcoming planned outages." });
			});
		}

		function updateRecentOutages() {
			return $.ajax({
				type: "POST",
				url: urlMainPhp,
				data: { menuAction: "OutageService.GetRecentOutages" },
				dataType: "json"
			}).done(function(data) {
				$.each(data.outages, function(k, v) {
					v.getIcon = ko.computed(function() {
						var classNames = ["glyphicon"];

						if (v.down == "Y") {
							if (v.planned == "Y") {
								classNames.push("glyphicon-info-sign");
								classNames.push("text-danger");
							}
							else {
								classNames.push("glyphicon-exclamation-sign");
								classNames.push("text-danger");
							}
						}
						else {
							if (v.planned == "Y") {
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
				});

				recentOutageViewModel.outages(data.outages);
				pieData.outages[2] = { label: "Recent Outages: " + data.outages.length, data: data.outages.length, href: "#recentOutages" };

				if (data.outages.length > 0) {
					$("#recentOutages").show('fade');
				}
			}).error(function() {
				$.gritter.add({ title: "Error", text: "Could not read recent outages." });
			});
		}
		{/if}

		function updateDashboard() {
			$.ajax({
				type: "POST",
				url: urlMainPhp,
				data: { menuAction: "DashboardService.GetData" },
				dataType: "json"
			}).done(function(data) {
				if (data.status !== undefined) {
					$.gritter.add({ title: data.status, text: data.message });
				} else {
					pieData.workOrders = [
						{ label: "Work Orders: " + data.workorders, data: data.workorders, href: "{$URL_MAIN_PHP}?menuAction=WorkOrder.Browse" },
						{ label: "My Work Orders: " + data.myWorkorders, data: data.myWorkorders, href: "{$URL_MAIN_PHP}?menuAction=WorkOrder.SearchMy" },
						{ label: "My Submitted: " + data.submittedWorkorders, data: data.submittedWorkorders, href: "{$URL_MAIN_PHP}?menuAction=WorkOrder.SearchMyCreated" }
					];

					pieData.tickets = [
						{ label: "Tickets: " + data.tickets, data: data.tickets, href: "{$URL_MAIN_PHP}?menuAction=htmlTickets.show" },
						{ label: "My Tickets: " + data.myTickets, data: data.myTickets, href: "{$URL_MAIN_PHP}?menuAction=htmlTickets.show&filterReportto={$VAL_USERID}" },
						{ label: "My Submitted: " + data.submittedTickets, data: data.submittedTickets, href: "{$URL_MAIN_PHP}?menuAction=htmlTickets.showSubmissions" }
					];

					pieData.projects = [
						{ label: "Projects: " + data.projects, data: data.projects, href: "{$URL_MAIN_PHP}?menuAction=Project.Index" },
						{ label: "My Projects: " + data.myProjects, data: data.myProjects, href: "{$URL_MAIN_PHP}?menuAction=Project.Index&filterReportto={$VAL_USERID}" }
					];

					$.plot("#workorders_chart", pieData.workOrders, pieOptions);
					$.plot("#tickets_chart", pieData.tickets, pieOptions);
					$.plot("#projects_chart", pieData.projects, pieOptions);

					$("#workorders_chart").bind("plotclick", function(event, pos, obj) {
						if (!obj)
							return;

						location.href = pieData.workOrders[obj.seriesIndex].href;
					});

					$("#tickets_chart").bind("plotclick", function(event, pos, obj) {
						if (!obj)
							return;

						location.href = pieData.tickets[obj.seriesIndex].href;
					});

					$("#projects_chart").bind("plotclick", function(event, pos, obj) {
						if (!obj)
							return;

						location.href = pieData.projects[obj.seriesIndex].href;
					});

					function workOrderHover(event, pos, item) {
						doHover(getHoverDescription(pieData.workOrders, item), pos, item);
					}

					function ticketHover(event, pos, item) {
						doHover(getHoverDescription(pieData.tickets, item), pos, item);
					}

					function projectHover(event, pos, item) {
						doHover(getHoverDescription(pieData.projects, item), pos, item);
					}

					$("#workorders_chart").bind("plothover", workOrderHover);
					$("#tickets_chart").bind("plothover", ticketHover);
					$("#projects_chart").bind("plothover", projectHover);

					$("#content").on("click", ".legendLabel", function() {
						var $this = $(this);
						var seriesIndex = $this.parent().index();
						var $chartDiv = $this.parents("div.dcl-chart:first");
						var series = $chartDiv.attr("data-ref");
						if (pieData.hasOwnProperty(series) && seriesIndex < pieData[series].length) {
							if (pieData[series][seriesIndex].data > 0) {
								location.href = pieData[series][seriesIndex].href;
							} else {
								$.gritter.add({ title: "No Matches", text: "There are no items of that category to display." });
							}
						}
					});
				}
			}).error(function() {
				$.gritter.add({ title: "Error", text: "Could not read dashboard data." });
			});
		}

		updateDashboard();
		{if $PERM_OUTAGES}
		$.when(updateCurrentOutages(), updateUpcomingOutages(), updateRecentOutages()).done(function () {
			$.plot("#outages_chart", pieData.outages, pieOptions);

			$("#outages_chart").bind("plotclick", function(event, pos, obj) {
				if (!obj)
					return;

				location.href = pieData.outages[obj.seriesIndex].href;
			});

			function outageHover(event, pos, item) {
				doHover(getHoverDescription(pieData.outages, item), pos, item);
			}

			$("#outages_chart").bind("plothover", outageHover);
		});
		{/if}
	});
</script>
{/block}