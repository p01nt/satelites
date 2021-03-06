{assign var=domains_by_day value=3}
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
			{if $satelites->countWrongIP() ne 0}
				<tr class="error"><td>Wrong IP: </td><td>{$satelites->countWrongIP()}</td></tr>
			{/if}
			{if $satelites->countWrongNS() ne 0}
				<tr class="error"><td>Wrong NS: </td><td>{$satelites->countWrongNS()}</td></tr>
			{/if}
			{if $satelites->countWrongNeedNS() ne 0}
				<tr class="error"><td>Wrong needed NS: </td><td>{$satelites->countWrongNeedNS()}</td></tr>
			{/if}
			{if $satelites->countWrongDatabase() ne 0}
				<tr class="error"><td>Without database: </td><td>{$satelites->countWrongDatabase()}</td></tr>
			{/if}
			{if $satelites->countWrongPostsAmount() ne 0}
				<tr class="error"><td>Without posts: </td><td>{$satelites->countWrongPostsAmount()}</td></tr>
			{/if}
			{if $satelites->countWrongURL() ne 0}
				<tr class="error"><td>Wrong URL: </td><td>{$satelites->countWrongURL()}</td></tr>
			{/if}
			{if $satelites->countWrongHomeUrl() ne 0}
				<tr class="error"><td>Wrong home URL: </td><td>{$satelites->countWrongHomeURL()}</td></tr>
			{/if}
			{if $satelites->countWrongTitle() ne 0}
				<tr class="error"><td>Wrong title: </td><td>{$satelites->countWrongTitle()}</td></tr>
			{/if}
			{if $satelites->countWrongPostsPerPage() ne 0}
				<tr class="error"><td>Wrong posts per page: </td><td>{$satelites->countWrongPostsPerPage()}</td></tr>
			{/if}
			{if $satelites->countWrongDescription() ne 0}
				<tr class="error"><td>Wrong description: </td><td>{$satelites->countWrongDescription()}</td></tr>
			{/if}
			<tr><td>Not registred: </td><td>{$satelites->countFree()}</td></tr>
			{if $satelites->countWrongWhois() ne 0}
				<tr class="error"><td>Wrong Whois: </td><td>{$satelites->countWrongWhois()}</td></tr>
			{/if}
			<tr><td>Total: </td><td>{$satelites->countTotal()}</td></tr>
			{assign var=forecast value=$smarty.now + ($satelites->countFree() - 1) / $domains_by_day * 3600 * 24}
			<tr><td>Completion forecast date: </td><td>{$forecast|date_format}</td></tr>
		</table>
		{assign var=interval value=$timer->interval()}

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
		{assign var=interval value=$timer->interval()}

		<h1 id="onlyMy">Registred by me</h1>
		<table class="list">
			{foreach from=$satelites->onlyMy() item=satelite}
				<tr>
					<td><a href="http://{$satelite->getName()}/" target="_blank">Go!</a></td>
					<td>{$satelite->getName()}</td>
					<td class="{if !$satelite->whois()->isMyNS()}error{/if}">{$satelite->whois()->printNS()}</td>
					<td class="{if !$satelite->ns()->isMyIP()}error{/if}">{if $satelite->ns()->getIP()}{$satelite->ns()->getIP()}{else}can't detect ip{/if}</td>
					<td class="{if !$satelite->ns()->isMyIPOnNeedNS()}error{/if}">{$satelite->ns()->printMyBadNeedNS()}</td>
					<td class="{if !$satelite->blog()->issetDatabase()}error{/if}">{if $satelite->blog()->issetDatabase()}{$satelite->blog()->getDBName()}{else}database non-exists{/if}</td>
					<td class="{if $satelite->blog()->wrongURL()}error{/if}">
						{if $satelite->blog()->getURL()}
							{$satelite->blog()->getURL()}
						{else}
							-
						{/if}
					</td>
					<td class="{if $satelite->blog()->wrongHomeURL()}error{/if}">
						{if $satelite->blog()->getHomeURL()}
							{$satelite->blog()->getHomeURL()}
						{else}
							-
						{/if}
					</td>
					<td class="{if $satelite->blog()->wrongTitle()}error{/if}">
						{if $satelite->blog()->getTitle()}
							{$satelite->blog()->getTitle()}
						{else}
							-
						{/if}
					</td>
					<td class="{if $satelite->blog()->wrongDescription()}error{/if}">
						{if $satelite->blog()->getDescription()}
							{$satelite->blog()->getDescription()}
						{else}
							-
						{/if}
					</td>
					<td class="{if $satelite->blog()->wrongPostsPerPage()}error{/if}">
						{if $satelite->blog()->getPostsPerPage()}
							{$satelite->blog()->getPostsPerPage()}
						{else}
							-
						{/if}
					</td>
					<td class="{if $satelite->blog()->noEnoughPosts()}error{/if}">{$satelite->blog()->postsAmount()}</td>
				</tr>
			{/foreach}
		</table>
		{assign var=interval value=$timer->interval()}

		<h1 id="onlyFree">Not registred</h1>
		<table class="list">
			{foreach from=$satelites->onlyFree() item=satelite name=free}
				{assign var=forecast_date value=$smarty.now + $smarty.foreach.free.index * 3600 * 24 / $domains_by_day}
				<tr class="{if $smarty.foreach.free.index % ($domains_by_day * 2) lt $domains_by_day}darker{/if}">
					{if $smarty.foreach.free.index % $domains_by_day eq 0}
						<td rowspan="{$domains_by_day}">{$forecast_date|date_format}</td>
					{/if}
					<td>{$satelite->getName()}</td>
					{*
					<td class="{if !$satelite->ns()->isMyIPOnNeedNS()}error{/if}">{$satelite->ns()->printMyBadNeedNS()}</td>
					<td class="{if !$satelite->blog()->issetDatabase()}error{/if}">{if $satelite->blog()->issetDatabase()}{$satelite->blog()->getDBName()}{else}database non-exists{/if}</td>
					<td class="{if $satelite->blog()->wrongURL()}error{/if}">{$satelite->blog()->getURL()}</td>
					<td class="{if $satelite->blog()->noEnoughPosts()}error{/if}">{$satelite->blog()->postsAmount()}</td>
					*}
				</tr>
			{/foreach}
		</table>
		{assign var=interval value=$timer->interval()}

		<div class="debug">
			<table>
				{foreach from=$timer->results() key=i item=interval}
					<tr>
						<td>{$i}</td>
						<td>{$interval.file}:<strong>{$interval.line}</strong></td>
						<td>{$interval.comment}</td>
						<td>{$interval.time}</td>
					</tr>
				{/foreach}
				<tr>
					<th colspan="3">Total</th>
					<th>{$timer->getTotal()}</th>

				</tr>
			</table>
		</div>
</body>
</html>
