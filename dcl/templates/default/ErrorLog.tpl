<div id="error-log">
	<h4>Error Log <small><span data-bind="text: refreshed"></span> <span class="badge alert-danger" data-bind="text: records"></span></small></h4>
	<ul id="pager" class="pager">
		<li class="previous"><a href="javascript:;">&larr; Newer</a></li>
		<li class="next"><a href="javascript:;">Older &rarr;</a></li>
	</ul>
	<table class="table table-striped">
		<thead>
			<tr><th>ID</th><th>Time</th><th>Level</th><th>User</th><th>Server</th><th>URI</th><th>File</th><th>Line</th><th>Error</th></tr>
		</thead>
		<tbody id="error-list" data-bind="foreach: rows">
		<tr>
			<td><a href="javascript:;" data-bind="text: id, attr: { 'data-error-id': id }"></a></td>
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
</div>
<div id="dialog" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Error Log ID <span data-bind="text: errorLogId"></span> <small data-bind="text: errorTimestamp"></small></h4>
			</div>
			<div class="modal-body">
				<div id="main-view" class="collapse in">
					<div data-bind="css: logLevelCss">
						<div class="panel-heading"><strong data-bind="text: logLevelText()"></strong> <span data-bind="text: errorDescription"></span></div>
						<ul class="list-group">
							<li class="list-group-item"><strong>User:</strong> <span data-bind="text: userId"></span></li>
							<li class="list-group-item"><strong>Server:</strong> <span data-bind="text: serverName"></span></li>
							<li class="list-group-item"><strong>Script:</strong> <span data-bind="text: scriptName"></span></li>
							<li class="list-group-item"><strong>File:</strong> <span data-bind="text: errorFile"></span></li>
							<li class="list-group-item"><strong>Line:</strong> <span data-bind="text: errorLine"></span></li>
							<li class="list-group-item"><strong>Request URI:</strong> <span data-bind="text: requestUri"></span></li>
							<li class="list-group-item"><strong>Query String:</strong> <span data-bind="text: queryString"></span></li>
						</ul>
					</div>
					<div data-bind="if: stackTrace">
						<div class="panel panel-default">
							<div class="panel-heading">Stack Trace</div>
							<table class="table">
								<thead><tr><th>#</th><th>Method</th><th>File</th><th>Line</th></tr></thead>
								<tbody data-bind="foreach: stackTrace">
									<tr>
										<td data-bind="text: $index"></td>
										<td><!-- ko text: $data['class'] --><!-- /ko --><!-- ko text: $data['type'] --><!-- /ko --><!-- ko text: $data['function'] --><!-- /ko -->(<!-- ko if: $data['object'] || (args && args.length) --><a href="javascript:;" class="stack-view-more" data-bind="attr: { 'data-id': $index }"> more&hellip; </a><!-- /ko -->)</td>
										<td data-bind="text: file"></td>
										<td data-bind="text: line"></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div id="stack-view" class="collapse">
					<a class="btn btn-default" href="javascript:;" id="back-to-main-view"><span class="glyphicon glyphicon-backward"></span> Back</a>
					<h4>Args</h4>
					<pre id="stack-view-args"></pre>
					<h4>Object</h4>
					<pre id="stack-view-object"></pre>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="{$DIR_VENDOR}knockout/knockout-3.1.0.js"></script>
<script type="text/javascript">
	$(function() {
		var logLevel = [ "Unknown", "Trace", "Debug", "Info", "Warning", "Error", "Fatal" ];
		var logLevelAlert = [ "info", "info", "info", "info", "warning", "danger", "danger" ];

		var errorViewModel = {
			refreshed: ko.observable(""),
			records: ko.observable(""),
			page: ko.observable(0),
			total: ko.observable(0),
			rows: ko.observableArray([])
		};

		ko.applyBindings(errorViewModel, document.getElementById("error-log"));

		var urlMainPhp = "{$URL_MAIN_PHP}";

		var firstId = 0;
		var lastId = 0;
		function updateErrorLog(dir) {
			$.ajax({
				type: "POST",
				url: urlMainPhp,
				data: { menuAction: "ErrorLogService.GetData", rows: 25, lastid: lastId, firstid: firstId, dir: dir },
				dataType: "json"
			}).done(function(data) {
				errorViewModel.records(data.records);
				errorViewModel.page(data.page);
				errorViewModel.total(data.total);
				errorViewModel.rows(data.rows);

				if (data.rows.length > 0) {
					lastId = data.rows[data.rows.length - 1].id;
					firstId = data.rows[0].id;
				} else {
					lastId = 0;
					firstId = 0;
				}

				if (data.min > 0 && data.min >= lastId)
					$("#pager").find("li.next").addClass("disabled");
				else
					$("#pager").find("li.next").removeClass("disabled");

				if (data.max > 0 && firstId >= data.max)
					$("#pager").find("li.previous").addClass("disabled");
				else
					$("#pager").find("li.previous").removeClass("disabled");
			}).error(function() {
				$.gritter.add({ title: "Error", text: "Could not read error log." });
				errorViewModel.refreshed("Error");
			});
		}

		updateErrorLog("next");

		var $pager = $("#pager");
		$pager.find("li.previous").click(function() {
			if (!$(this).hasClass("disabled"))
				updateErrorLog("previous");
		});

		$pager.find("li.next").click(function() {
			if (!$(this).hasClass("disabled"))
				updateErrorLog("next");
		});

		function htmlEncode(t) {
			return $("<div/>").text(t).html();
		}

		var errorDetailModel = {
			errorLogId: ko.observable(0),
			errorTimestamp: ko.observable(""),
			userId: ko.observable(0),
			serverName: ko.observable(""),
			scriptName: ko.observable(""),
			requestUri: ko.observable(""),
			queryString: ko.observable(""),
			errorFile: ko.observable(""),
			errorLine: ko.observable(""),
			errorDescription: ko.observable(""),
			stackTrace: ko.observableArray([]),
			logLevel: ko.observable(0)
		};

		errorDetailModel.logLevelText = ko.computed(function() {
			return logLevel[this.logLevel()];
		}, errorDetailModel);

		errorDetailModel.logLevelCss = ko.computed(function() {
			return "panel panel-" + logLevelAlert[this.logLevel()];
		}, errorDetailModel);

		ko.applyBindings(errorDetailModel, document.getElementById("dialog"));

		$("#error-list").on("click", "a", function(e) {
			e.preventDefault();
			var id = $(this).attr("data-error-id");
			if (id == "")
				return;

			$.ajax({
				type: "POST",
				url: urlMainPhp,
				data: { menuAction: "ErrorLogService.Item", id: id },
				dataType: "json"
			}).done(function(data) {
				errorDetailModel.errorLogId(data.error_log_id);
				errorDetailModel.errorTimestamp(data.error_timestamp);
				errorDetailModel.userId(data.user_id);
				errorDetailModel.serverName(data.server_name);
				errorDetailModel.scriptName(data.script_name);
				errorDetailModel.requestUri(data.request_uri);
				errorDetailModel.queryString(data.query_string);
				errorDetailModel.errorFile(data.error_file);
				errorDetailModel.errorLine(data.error_line);
				errorDetailModel.errorDescription(data.error_description);
				errorDetailModel.stackTrace(data.stack_trace);
				errorDetailModel.logLevel(data.log_level);
				$("#dialog").modal();
			}).error(function() {
				$.gritter.add({
					title: "Error",
					text: "Could not retrieve error log entry " + id
				});
			}).always(function() {
			});
		});

		$("#back-to-main-view").click(function() {
			$("#main-view,#stack-view").collapse("toggle");
		});

		$("#main-view").on("click", "a.stack-view-more", function(e) {
			e.preventDefault();
			var id = $(this).attr("data-id");
			var stackTrace = errorDetailModel.stackTrace();
			if (id == "" || stackTrace.length == 0 || id > stackTrace.length - 1)
				return;

			if ("args" in stackTrace[id]) {
				$("#stack-view-args").text(JSON.stringify(stackTrace[id].args, null, 2));
			} else {
				$("#stack-view-args").text("None");
			}

			if ("object" in stackTrace[id]) {
				$("#stack-view-object").text(JSON.stringify(stackTrace[id].object, null, 2));
			} else {
				$("#stack-view-object").text("None");
			}

			$("#main-view,#stack-view").collapse("toggle");
		});
	});
</script>