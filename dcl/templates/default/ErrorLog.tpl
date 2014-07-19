<h4>Error Log <small><span data-bind="text: refreshed"></span> <span class="badge alert-danger" data-bind="text: records"></span></small></h4>
<table class="table table-striped">
	<thead>
		<tr><th>ID</th><th>Time</th><th>Level</th><th>User</th><th>Server</th><th>URI</th><th>File</th><th>Line</th><th>Error</th></tr>
	</thead>
	<tbody data-bind="foreach: rows">
	<tr>
		<td data-bind="text: id"></td>
		<td data-bind="text: ts"></td>
		<td data-bind="text: lvl"></td>
		<td data-bind="text: user"></td>
		<td data-bind="text: srv"></td>
		<td data-bind="text: uri"></td>
		<td data-bind="text: file"></td>
		<td data-bind="text: line"></td>
		<td data-bind="text: desc"></td>
	</tr>
	</tbody>
</table>
<script type="text/javascript" src="{$DIR_VENDOR}knockout/knockout-3.1.0.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}moment/moment.min.js"></script>
<script type="text/javascript">
	$(function() {
		var errorViewModel = {
			refreshed: ko.observable(""),
			records: ko.observable(""),
			page: ko.observable(0),
			total: ko.observable(0),
			rows: ko.observableArray([])
		};

		ko.applyBindings(errorViewModel);

		var urlMainPhp = "{$URL_MAIN_PHP}";

		function updateErrorLog() {
			$.ajax({
				type: "POST",
				url: urlMainPhp,
				data: { menuAction: "ErrorLogService.GetData", rows: 25 },
				dataType: "json"
			}).done(function(data) {
				errorViewModel.records(data.records);
				errorViewModel.page(data.page);
				errorViewModel.total(data.total);
				errorViewModel.rows(data.rows);
				errorViewModel.refreshed(moment().format('HH:mm:ss'));
			}).error(function() {
				$.gritter.add({ title: "Error", text: "Could not read error log." });
				errorViewModel.refreshed("Error");
			}).always(function() {
				setTimeout(updateErrorLog, 30000);
			});
		}

		updateErrorLog();
	});
</script>