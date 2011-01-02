<table border="1">
	{foreach from=$satelites item=satelite}
	<tr>
		<td>{$satelite->getName()}</td>
	</tr>
	{/foreach}
</table>
