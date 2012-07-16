<link type="text/css" rel="stylesheet" href="{$DIR_JS}tagedit/css/jquery.tagedit.css" />
<style type="text/css">{literal}
	#autocomplete-form p button, #autocomplete-form p ul { float: left; margin-right: 4px; }
	#autocomplete-form p { margin: 0px; }
	#autocomplete-form p ul { margin-top: 2px; }
{/literal}</style>
<div class="dcl_detail">
	<table class="styled">
		<caption>[{$VAL_PROJECTID}] {$VAL_NAME|escape}</caption>
		<thead>{include file="ProjectOptionsControl.tpl"}</thead>
		<tbody>
			<tr><td colspan="4">
					<form method="POST" action="" id="post-form">
						<input type="hidden" name="children" id="children" />
					</form>
					<form method="POST" action="" id="autocomplete-form">
						<p>
							<button class="btn" id="include-children">Include Projects</button>
							{section name=project loop=$VAL_INCLUDEDPROJECTS}<input type="text" name="children-ac[{$VAL_INCLUDEDPROJECTS[project].id}-a]" value="[{$VAL_INCLUDEDPROJECTS[project].id}] {$VAL_INCLUDEDPROJECTS[project].name|escape}" class="tag" />{/section}
							<input type="text" name="children-ac[]" class="tag" />
						</p>
					</form>
				</td>
			</tr>
			<tr><td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=ProjectImage.StatusChart&id={$VAL_PROJECTID}{if $VAL_PROJECTCHILDREN}&children={$VAL_PROJECTCHILDREN}{/if}"></td>
				<td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=ProjectImage.DepartmentChart&id={$VAL_PROJECTID}{if $VAL_PROJECTCHILDREN}&children={$VAL_PROJECTCHILDREN}{/if}"></td>
			</tr>
			<tr><td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=ProjectImage.SeverityChart&id={$VAL_PROJECTID}{if $VAL_PROJECTCHILDREN}&children={$VAL_PROJECTCHILDREN}{/if}"></td>
				<td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=ProjectImage.PriorityChart&id={$VAL_PROJECTID}{if $VAL_PROJECTCHILDREN}&children={$VAL_PROJECTCHILDREN}{/if}"></td>
			<tr>
			<tr><td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=ProjectImage.ModuleChart&id={$VAL_PROJECTID}{if $VAL_PROJECTCHILDREN}&children={$VAL_PROJECTCHILDREN}{/if}"></td>
				<td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=ProjectImage.TypeChart&id={$VAL_PROJECTID}{if $VAL_PROJECTCHILDREN}&children={$VAL_PROJECTCHILDREN}{/if}"></td>
			<tr>
		</tbody>
	</table>
</div>
<script type="text/javascript" src="{$DIR_JS}tagedit/js/jquery.autoGrowInput.js"></script>
<script type="text/javascript" src="{$DIR_JS}tagedit/js/jquery.tagedit.js"></script>
<script type="text/javascript">{literal}
	$(document).ready(function() {
		$("#autocomplete-form").find("input.tag").tagedit({
				autocompleteURL: "{/literal}{dcl_url_action controller=ProjectService action=Autocomplete}{literal}",
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
{/literal}</script>