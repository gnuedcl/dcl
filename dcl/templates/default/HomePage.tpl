{if $PERM_OUTAGES}
<div id="currentOutages" data-bind="if: outages().length">
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
<div id="upcomingOutages" data-bind="if: outages().length">
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
<div id="recentOutages" data-bind="if: outages().length">
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
<script type="text/javascript" src="{$DIR_VENDOR}knockout/knockout-3.3.0.js"></script>
<script type="text/javascript">
	$(function() {
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

		var outagePanelClasses = ["panel-danger", "panel-warning", "panel-success"];
		var outagePanelClass = 2;

		var urlMainPhp = "{$URL_MAIN_PHP}";
		function updateCurrentOutages() {
			$.ajax({
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
			}).error(function() {
				$.gritter.add({ title: "Error", text: "Could not read current outages." });
			});
		}

		function updateUpcomingOutages() {
			$.ajax({
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
			}).error(function() {
				$.gritter.add({ title: "Error", text: "Could not read upcoming planned outages." });
			});
		}

		function updateRecentOutages() {
			$.ajax({
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
			}).error(function() {
				$.gritter.add({ title: "Error", text: "Could not read recent outages." });
			});
		}

		updateCurrentOutages();
		updateUpcomingOutages();
		updateRecentOutages();
	});
</script>
{/if}