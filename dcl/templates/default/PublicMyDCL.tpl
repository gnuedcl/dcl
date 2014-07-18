<style>
	.grid-panel {
		text-align: center;
		padding: 20px;
		border-radius: 5px;
		background-color: #ececec;
	}

	.grid-panel h2 {
		margin-top: 1px;
	}
</style>
<h3>DCL Public Interface</h3>
<div class="container">
	<div class="row">
	{if $PERM_TICKETS}
		<div class="col-sm-4">
			<div class="grid-panel">
				<h2>Tickets</h2>
				<p>View tickets associated with your contact record or created by you</p>
				<a class="btn btn-primary" href="{$URL_MENULINK}?menuAction=htmlTickets.show">{$smarty.const.STR_CMMN_GO|escape}</a>
			</div>
		</div>
	{/if}
	{if $PERM_WORKORDERS}
		<div class="col-sm-4">
			<div class="grid-panel">
				<h2>Work Orders</h2>
				<p>View work orders associated with your contact record or created by you</p>
				<a class="btn btn-primary" href="{$URL_MENULINK}?menuAction=WorkOrder.Browse">{$smarty.const.STR_CMMN_GO|escape}</a>
			</div>
		</div>
	{/if}
	{if $PERM_FAQ}
		<div class="col-sm-4">
			<div class="grid-panel">
				<h2>FAQs</h2>
				<p>Browse the knowledge base of frequently asked questions</p>
				<a class="btn btn-primary" href="{$URL_MENULINK}?menuAction=Faq.Index">{$smarty.const.STR_CMMN_GO|escape}</a>
			</div>
		</div>
	{/if}
	</div>
</div>
