<table align"{dir}">
	{foreach from=$plugin->series($from, $to) key='name' item='title'}
		<tr>
		    <td>
			{$title}:
		    </td>
		    <td>
			{$data.$name}
		    </td>
		</tr>
	{/foreach}
</table>
