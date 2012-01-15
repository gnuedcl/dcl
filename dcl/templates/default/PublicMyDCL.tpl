<table>
	<tr><th class="detailTitle" colspan="2" style="width:80%;">DCL Public Interface</th></tr>
	{if $PERM_TICKETS}
	<tr><td><a href="{$URL_MENULINK}?menuAction=htmlTickets.show">Tickets</a></td>
		<td>View tickets associated with your contact record or created by you</td>
	</tr>
	{/if}
	{if $PERM_WORKORDERS}
	<tr><td><a href="{$URL_MENULINK}?menuAction=WorkOrder.Browse">Work Orders</a></td>
		<td>View work orders associated with your contact record or created by you</td>
	</tr>
	{/if}
	{if $PERM_FAQ}
	<tr><td><a href="{$URL_MENULINK}?menuAction=Faq.Index">FAQs</a></td>
		<td>Browse the knowledge base of frequently asked questions</td>
	</tr>
	{/if}
</table>