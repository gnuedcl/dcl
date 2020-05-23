{extends file="_Layout.tpl"}
{block name=title}[{$VAL_PROJECTID}] {$VAL_NAME|escape}{/block}
{block name=css}
<link type="text/css" rel="stylesheet" href="{$DIR_VENDOR}tagedit/css/jquery.tagedit.css" />
<style type="text/css">
	#autocomplete-form p button, #autocomplete-form p ul { float: left; margin-right: 4px; }
	#autocomplete-form p { margin: 0; }
	#autocomplete-form p ul { margin-top: 2px; }
</style>
{/block}
{block name=content}
<h4>[{$VAL_PROJECTID}] {$VAL_NAME|escape}</h4>
{include file="ProjectOptionsControl.tpl"}
<div class="container">
	<div class="row top12">
		<div class="col-xs-12">
			<form method="POST" action="" id="post-form">
				<input type="hidden" name="children" id="children" />
			</form>
			<form class="form-inline" method="POST" action="" id="autocomplete-form">
				<p>
					<button class="btn btn-default" id="include-children">Include Projects</button>
					{section name=project loop=$VAL_INCLUDEDPROJECTS}<input type="text" name="children-ac[{$VAL_INCLUDEDPROJECTS[project].id}-a]" value="[{$VAL_INCLUDEDPROJECTS[project].id}] {$VAL_INCLUDEDPROJECTS[project].name|escape}" class="tag" />{/section}
					<input type="text" name="children-ac[]" class="tag" />
				</p>
			</form>
		</div>
	</div>
	<div class="row top12">
		<div class="col-xs-6"><img src="{$URL_MAIN_PHP}?menuAction=ProjectImage.StatusChart&id={$VAL_PROJECTID}{if $VAL_PROJECTCHILDREN}&children={$VAL_PROJECTCHILDREN}{/if}"></div>
		<div class="col-xs-6"><img src="{$URL_MAIN_PHP}?menuAction=ProjectImage.DepartmentChart&id={$VAL_PROJECTID}{if $VAL_PROJECTCHILDREN}&children={$VAL_PROJECTCHILDREN}{/if}"></div>
	</div>
	<div class="row top12">
		<div class="col-xs-6"><img src="{$URL_MAIN_PHP}?menuAction=ProjectImage.SeverityChart&id={$VAL_PROJECTID}{if $VAL_PROJECTCHILDREN}&children={$VAL_PROJECTCHILDREN}{/if}"></div>
		<div class="col-xs-6"><img src="{$URL_MAIN_PHP}?menuAction=ProjectImage.PriorityChart&id={$VAL_PROJECTID}{if $VAL_PROJECTCHILDREN}&children={$VAL_PROJECTCHILDREN}{/if}"></div>
	</div>
	<div class="row top12">
		<div class="col-xs-6"><img src="{$URL_MAIN_PHP}?menuAction=ProjectImage.ModuleChart&id={$VAL_PROJECTID}{if $VAL_PROJECTCHILDREN}&children={$VAL_PROJECTCHILDREN}{/if}"></div>
		<div class="col-xs-6"><img src="{$URL_MAIN_PHP}?menuAction=ProjectImage.TypeChart&id={$VAL_PROJECTID}{if $VAL_PROJECTCHILDREN}&children={$VAL_PROJECTCHILDREN}{/if}"></div>
	</div>
</div>
{/block}
{block name=script}
<script type="text/javascript" src="{$DIR_VENDOR}tagedit/js/jquery.autoGrowInput.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}tagedit/js/jquery.tagedit.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#autocomplete-form").find("input.tag").tagedit({
				autocompleteURL: "{dcl_url_action controller=ProjectService action=Autocomplete}",
				allowEdit: false,
				allowAdd: false
		});

		$("#include-children").click(function(e) {
			e.preventDefault();

			$("#children").val($("#autocomplete-form input[type=hidden]").map(function() {
				var name = this.name;
				var leftIdx = name.indexOf("[") + 1;
				if (leftIdx > 0) {
					var rightIdx = name.indexOf("-", leftIdx);
					if (rightIdx > leftIdx) {
						return name.substr(leftIdx, rightIdx - leftIdx);
					}
				}

				return "-1";
			}).get().join(','));

			$("#post-form").submit();
		});
	});
</script>
{/block}