<ul>
	<li><h3>{$VAL_TITLE}</h3>
		<ul>{section name=navboxitem loop=$VAL_NAVBOXITEMS}<li><a href="{$VAL_NAVBOXITEMS[navboxitem].onclick}">{$VAL_NAVBOXITEMS[navboxitem].text}</a></li>{/section}</ul>
	</li>
</ul>
