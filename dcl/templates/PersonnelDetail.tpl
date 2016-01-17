{extends file="_Layout.tpl"}
{block name=content}
<h1>{dcl_gravatar userId=$Personnel->id style="margin-right:4px;"} {$Contact->first_name|escape} {$Contact->last_name|escape} ({$Personnel->short|escape})</h1>
<ul class="nav nav-tabs">
	<li class="active"><a href="#tab-1" data-toggle="tab">Dashboard</a></li>
	<li><a href="#tab-2" data-toggle="tab">Recent Activity</a></li>
</ul>
<div class="tab-content">
	<div id="tab-1" class="tab-pane fade in active">
		<div class="container">
			<div class="row top12">
				<div class="col-sm-6"><img src="{$URL_MAIN_PHP}?menuAction=PersonnelImage.ProductChart&id={$Personnel->id}"></div>
				<div class="col-sm-6"><img src="{$URL_MAIN_PHP}?menuAction=PersonnelImage.StatusChart&id={$Personnel->id}"></div>
			</div>
			<div class="row top12">
				<div class="col-sm-6"><img src="{$URL_MAIN_PHP}?menuAction=PersonnelImage.SeverityChart&id={$Personnel->id}"></div>
				<div class="col-sm-6"><img src="{$URL_MAIN_PHP}?menuAction=PersonnelImage.PriorityChart&id={$Personnel->id}"></div>
			</div>
			<div class="row top12">
				<div class="col-sm-6"><img src="{$URL_MAIN_PHP}?menuAction=PersonnelImage.TypeChart&id={$Personnel->id}"></div>
			</div>
		</div>
	</div>
	<div id="tab-2" class="tab-pane fade">
		{$VAL_RECENTACTIVITY}
	</div>
</div>
{/block}