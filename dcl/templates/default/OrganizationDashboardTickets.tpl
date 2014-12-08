<h4>Organization [{Org->org_id}] {Org->name|escape}</h4>
<div class="btn-group">
	{if $PERM_VIEW}
		<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlOrgBrowse.show&filterActive=Y">Browse</a>
		<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Organization.Detail&org_id={Org->org_id}">Detail</a>
	{/if}
	{if $PERM_VIEW_CONTACT}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlContactBrowse.show&filterActive=Y&org_id={Org->org_id}">Contacts</a>{/if}
	{if $PERM_VIEW_WORKORDER || $PERM_VIEW_TICKET}
		{if $PERM_VIEW_WORKORDER}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlOrganizationDashboard.Show&id={Org->org_id}">Dashboard</a>{/if}
		{if $PERM_VIEW_TICKET}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlOrganizationDashboard.ShowTicket&id={Org->org_id}">Ticket Dashboard</a>{/if}
		{if $PERM_VIEW_MEASUREMENT}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Organization.Measurement&org_id={Org->org_id}">Measurements</a>{/if}
		{if $PERM_VIEW_OUTAGE}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Organization.Outage&org_id={Org->org_id}">Outages</a>{/if}
	{/if}
	{if $PERM_VIEW_WORKORDER}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlOrg.viewWorkOrders&id={Org->org_id}">Work Orders</a>
		<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=boWatches.add&typeid=6&whatid1={Org->org_id}">Watch</a>
	{/if}
	{if $PERM_VIEW_TICKET}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlOrg.viewTickets&id={Org->org_id}">Tickets</a>
		<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=boWatches.add&typeid=7&whatid1={Org->org_id}">Watch</a>
	{/if}
	{if $PERM_WIKI}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlWiki.show&type=5&id={Org->org_id}">Wiki</a>{/if}
	{if $PERM_MODIFY}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Organization.Edit&org_id={Org->org_id}">Edit</a>{/if}
	{if $PERM_DELETE}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Organization.Delete&org_id={Org->org_id}">Delete</a>{/if}
</div>
<div class="container">
	<div class="row top12">
		<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=OrganizationImage.TicketStatusChart&id={Org->org_id}"></div>
		<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=OrganizationImage.TicketModuleChart&id={Org->org_id}"></div>
	</div>
	<div class="row top12">
		<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=OrganizationImage.TicketTypeChart&id={Org->org_id}"></div>
		<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=OrganizationImage.TicketPriorityChart&id={Org->org_id}"></div>
	</div>
</div>