{include file='web/header.tpl' title='Bestellen'}

<p>
	<u>Speiseplan:</u>
</p>
<table>
	<tr>
		<td><p>Diese Woche</p>{$message}</td>
	</tr>
	<tr>
		{foreach $meals as $meal} {if {$meal.kalenderwoche} eq
		{$smarty.now|date_format:"%W"}}
		<td><p>
				{$meal.wochentag}<br>{$meal.date}<br> <a
					href="index.php?section=order&order={$meal.ID}">{$meal.name}</a>
			</p></td>
		<!-- <p>{$meal.date}: <a href="index.php?section=order&order={$meal.ID}">{$meal.name}</a></p>     TODO: Tabelle! Speiseplan soweit vorhanden dieser + n�chster Woche jeweils in einer Tabelle mit 2 Spalten:   -->
		{/if} {/foreach}
	</tr>
	<tr>
		<td><p>N&auml;chste Woche</p>{$message}</td>
		
	</tr>
	<tr>
		{foreach $meals as $meal} {if {$meal.kalenderwoche} eq
		{$smarty.now|date_format:"%W"+1}}
		<td><p>
				{$meal.wochentag}<br>{$meal.date}<br> <a
					href="index.php?section=order&order={$meal.ID}">{$meal.name}</a>
			</p></td>
		<!-- <p>{$meal.date}: <a href="index.php?section=order&order={$meal.ID}">{$meal.name}</a></p>     TODO: Tabelle! Speiseplan soweit vorhanden dieser + n�chster Woche jeweils in einer Tabelle mit 2 Spalten:   -->
		{/if} {/foreach}
	</tr>
</table>
<!-- oben die Wochentage, links men� 1, men� 2     -->
{include file='web/footer.tpl'}
