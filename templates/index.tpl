<html>
	<head>
		<link href="/css/style.css" rel="stylesheet" />
		<title>:: Satelites Monitor ::</title>
	</head>
	<body>
		<form>
			<input type="text" name="name" value="" />
			<input type="submit" value="Search" />
		</form>
		<div class="menu">
			[
				<a href="#onlyOthers">Registred by others</a>
				::
				<a href="#onlyMy">Registred by me</a>
				::
				<a href="#onlyFree">Not registred</a>
			]
		</div>

		<table>
			<tr><td>Registred by others: </td><td>{$satelites->countOthers()}</td></tr>
			<tr><td>Registred by me: </td><td>{$satelites->countMy()}</td></tr>
			<tr><td>Not registred: </td><td>{$satelites->countFree()}</td></tr>
			{if $satelites->countWrongIP() ne 0}
				<tr class="error"><td>Wrong IP: </td><td>{$satelites->countWrongIP()}</td></tr>
			{/if}
			{if $satelites->countWrongNS() ne 0}
				<tr class="error"><td>Wrong NS: </td><td>{$satelites->countWrongNS()}</td></tr>
			{/if}
			{if $satelites->countWrongNeedNS() ne 0}
				<tr class="error"><td>Wrong needed NS: </td><td>{$satelites->countWrongNeedNS()}</td></tr>
			{/if}
			{if $satelites->countWrongWhois() ne 0}
				<tr class="error"><td>Wrong WhoIs: </td><td>{$satelites->countWrongWhois()}</td></tr>
			{/if}
			<tr><td>Total: </td><td>{$satelites->countTotal()}</td></tr>
		</table>

		<h1 id="onlyOthers">Registred by others</h1>
		<table class="list">
			{foreach from=$satelites->onlyOthers() item=satelite}
				<tr>
					<td><a href="http://{$satelite->getName()}/" target="_blank">Go!</a></td>
					<td class="uppercase">{$satelite->getName()}</td>
					<td>{$satelite->whois()->getAdminC()}</td>
					<td>{$satelite->whois()->getTechC()}</td>
				</tr>
			{/foreach}
		</table>
		<h1 id="onlyMy">Registred by me</h1>
		<table class="list">
			{foreach from=$satelites->onlyMy() item=satelite}
				<tr>
					<td><a href="http://{$satelite->getName()}/" target="_blank">Go!</a></td>
					<td>{$satelite->getName()}</td>
					<td class="{if !$satelite->whois()->isMyNS()}error{/if}">{$satelite->whois()->printNS()}</td>
					<td class="{if !$satelite->ns()->isMyIP()}error{/if}">{$satelite->ns()->getIP()}</td>
					<td class="{if !$satelite->ns()->isMyIPOnNeedNS()}error{/if}">{$satelite->ns()->printMyBadNeedNS()}</td>
					<td class="{if !$satelite->blog()->issetDatabase()}error{/if}">database {if $satelite->blog()->issetDatabase()}exists{else}non-exists{/if}</td>
					<td class="{if $satelite->blog()->postsAmount() eq 0}error{/if}">{$satelite->blog()->postsAmount()}</td>
				</tr>
			{/foreach}
		</table>
		<h1 id="onlyFree">Not registred</h1>
		<table class="list">
			{foreach from=$satelites->onlyFree() item=satelite}
				<tr>
					<td>{$satelite->getName()}</td>
					<td class="{if !$satelite->ns()->isMyIPOnNeedNS()}error{/if}">{$satelite->ns()->printMyBadNeedNS()}</td>
					<td class="{if !$satelite->blog()->issetDatabase()}error{/if}">database {if $satelite->blog()->issetDatabase()}exists{else}non-exists{/if}</td>
					<td class="{if $satelite->blog()->postsAmount() eq 0}error{/if}">{$satelite->blog()->postsAmount()}</td>
				</tr>
			{/foreach}
		</table>
</body>
</html>
