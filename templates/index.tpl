<html>
	<head>
		<link href="/css/style.css" rel="stylesheet" />
	</head>
	<body>
		<table>
			{foreach from=$satelites item=satelite}
				<tr>
					<td>{$satelite->getName()}</td>
				</tr>
			{/foreach}
		</table>
</body>
</html>
