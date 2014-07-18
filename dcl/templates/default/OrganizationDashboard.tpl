<h4>Organization [{Org->org_id}] {Org->name|escape}</h4>
<div class="btn-group">
	<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlOrgBrowse.show&filterActive=Y">Browse</a>
	<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlContactBrowse.show&filterActive=Y&org_id={Org->org_id}">Contacts</a>
	<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlOrganizationDashboard.Show&id={Org->org_id}">Dashboard</a>
	<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlOrganizationDashboard.ShowTicket&id={Org->org_id}">Ticket Dashboard</a>
	<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlOrg.viewWorkOrders&id={Org->org_id}">Work Orders</a>
	<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=boWatches.add&typeid=6&whatid1={Org->org_id}">Watch</a>
	<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlOrg.viewTickets&id={Org->org_id}">Tickets</a>
	<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=boWatches.add&typeid=7&whatid1={Org->org_id}">Watch</a>
	<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlWiki.show&type=5&id={Org->org_id}">Wiki</a>
	<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Organization.Edit&org_id={Org->org_id}">Edit</a>
	<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Organization.Delete&org_id={Org->org_id}">Delete</a>
</div>
<div class="container">
	<div class="row top12">
		<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=OrganizationImage.WorkOrderStatusChart&id={Org->org_id}"></div>
		<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=OrganizationImage.WorkOrderDepartmentChart&id={Org->org_id}"></div>
	</div>
	<div class="row top12">
		<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=OrganizationImage.WorkOrderSeverityChart&id={Org->org_id}"></div>
		<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=OrganizationImage.WorkOrderPriorityChart&id={Org->org_id}"></div>
	</div>
	<div class="row top12">
		<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=OrganizationImage.WorkOrderModuleChart&id={Org->org_id}"></div>
		<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=OrganizationImage.WorkOrderTypeChart&id={Org->org_id}"></div>
	</div>
</div>