<!-- $Id$ -->
<div class="dcl_detail" style="width:100%;">
	<table class="styled">
	<caption>Organization [{Org->org_id}] {Org->name}</caption>
	<thead>
		<tr class="toolbar"><th>
			<ul>
				<li class="first"><a href="{$URL_MAIN_PHP}?menuAction=htmlOrgBrowse.show&filterActive=Y">Browse</a></li>
				<li><a href="{$URL_MAIN_PHP}?menuAction=htmlContactBrowse.show&filterActive=Y&org_id={Org->org_id}">Contacts</a></li>
				<li><a href="{$URL_MAIN_PHP}?menuAction=htmlOrganizationDashboard.Show&id={Org->org_id}">Dashboard</a> (<a href="{$URL_MAIN_PHP}?menuAction=htmlOrganizationDashboard.ShowTicket&id={Org->org_id}">Tickets</a>)</li>
				<li>
					<a href="{$URL_MAIN_PHP}?menuAction=htmlOrg.viewWorkOrders&id={Org->org_id}">Work Orders</a>
					&nbsp;(<a href="{$URL_MAIN_PHP}?menuAction=boWatches.add&typeid=6&whatid1={Org->org_id}">Watch</a>)
				</li>
				<li><a href="{$URL_MAIN_PHP}?menuAction=htmlOrg.viewTickets&id={Org->org_id}">Tickets</a>
					&nbsp;(<a href="{$URL_MAIN_PHP}?menuAction=boWatches.add&typeid=7&whatid1={Org->org_id}">Watch</a>)
				</li>
				<li><a href="{$URL_MAIN_PHP}?menuAction=htmlWiki.show&type=5&id={Org->org_id}">Wiki</a></li>
				<li><a href="{$URL_MAIN_PHP}?menuAction=Organization.Edit&org_id={Org->org_id}">Edit</a></li>
				<li><a href="{$URL_MAIN_PHP}?menuAction=Organization.Delete&org_id={Org->org_id}">Delete</a></li>
			</ul>
		</th></tr>
	</thead>
		<tbody>
			<tr><td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=OrganizationImage.WorkOrderStatusChart&id={Org->org_id}"></td>
				<td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=OrganizationImage.WorkOrderDepartmentChart&id={Org->org_id}"></td>
			</tr>
			<tr><td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=OrganizationImage.WorkOrderSeverityChart&id={Org->org_id}"></td>
				<td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=OrganizationImage.WorkOrderPriorityChart&id={Org->org_id}"></td>
			</tr>
			<tr><td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=OrganizationImage.WorkOrderModuleChart&id={Org->org_id}"></td>
				<td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=OrganizationImage.WorkOrderTypeChart&id={Org->org_id}"></td>
			</tr>
		</tbody>
	</table>
</div>