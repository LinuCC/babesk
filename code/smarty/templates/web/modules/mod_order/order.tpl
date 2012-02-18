{include file='web/header.tpl' title='Bestellen'}

<h2>
	<u>Speiseplan:</u>
</h2>

{literal}
<style type="text/css">
td {width:20%; background-color:#33CCFF; text-align: center;}
tr {width:20%; background-color:#FFCC33; text-align: center;}
table{width:100%;}
</style>
{/literal}

<center><h3>Diese Woche</h3>{$message}</center>
<table width="100%">
		
		<tr><td><p>Montag<br>{$thisMonday}</td><td ><p>Dienstag<br>{$thisTuesday}</td>
	<td ><p>Mittwoch<br>{$thisWednesday}</td><td ><p>Donnerstag<br>{$thisThursday}</td>
			<td ><p>Freitag<br>{$thisFriday}</td></tr>	
	<!-- montag -->
		<tr>
		<td>
		{foreach $meals as $meal}
			{if {$meal.kalenderwoche} eq {$smarty.now|date_format:"%W"}}
				{if {$meal.date} eq {$thisMonday}} 
					<ul><a href="index.php?section=order&order={$meal.ID}">{$meal.name}</a></ul>
				{/if}
			{/if}
		{/foreach}
		</p></td>
		
<!-- dienstag -->	
<td>	
		{foreach $meals as $meal}
			{if {$meal.kalenderwoche} eq {$smarty.now|date_format:"%W"}}
				{if {$meal.date} eq {$thisTuesday}} 
					<ul><a href="index.php?section=order&order={$meal.ID}">{$meal.name}</a></ul>
				{/if}
			{/if}
		{/foreach}
		</p></td>
		
<!-- mittwoch -->
<td>		
		{foreach $meals as $meal}
			{if {$meal.kalenderwoche} eq {$smarty.now|date_format:"%W"}}
				{if {$meal.date} eq {$thisWednesday}} 
					<ul><a href="index.php?section=order&order={$meal.ID}">{$meal.name}</a></ul>
				{/if}
			{/if}
		{/foreach}
		</p></td>
		
<!-- donnerstag -->
<td>
		{foreach $meals as $meal}
			{if {$meal.kalenderwoche} eq {$smarty.now|date_format:"%W"}}
				{if {$meal.date} eq {$thisThursday}} 
					<ul><a href="index.php?section=order&order={$meal.ID}">{$meal.name}</a></ul>
				{/if}
			{/if}
		{/foreach}
		</p></td>
		
<!-- freitag -->
<td>
		{foreach $meals as $meal}
			{if {$meal.kalenderwoche} eq {$smarty.now|date_format:"%W"}}
				{if {$meal.date} eq {$thisFriday}} 
					<ul><a href="index.php?section=order&order={$meal.ID}">{$meal.name}</a></ul>
				{/if}
			{/if}
		{/foreach}
		</p></td>
	</tr>
</table><br><br>
	<center><h3>N&auml;chste Woche</h3>{$message}</center>
	<table>
	
	<tr><td><p>Montag<br>{$nextMonday}</td><td><p>Dienstag<br>{$nextTuesday}</td>
	<td><p>Mittwoch<br>{$nextWednesday}</td><td><p>Donnerstag<br>{$nextThursday}</td>
			<td><p>Freitag<br>{$nextFriday}</td></tr>
		
<!-- montag -->
		<tr>
		<td>
		{foreach $meals as $meal}
			{if {$meal.kalenderwoche} eq {$smarty.now|date_format:"%W"+1}}
				{if {$meal.date} eq {$nextMonday}} 
					<ul><a href="index.php?section=order&order={$meal.ID}">{$meal.name}</a></ul>
				{/if}
			{/if}
		{/foreach}
		</p></td>
		
<!-- dienstag -->	
<td>	
		{foreach $meals as $meal}
			{if {$meal.kalenderwoche} eq {$smarty.now|date_format:"%W"+1}}
				{if {$meal.date} eq {$nextTuesday}} 
					<ul><a href="index.php?section=order&order={$meal.ID}">{$meal.name}</a></ul>
				{/if}
			{/if}
		{/foreach}
		</p></td>
		
<!-- mittwoch -->
<td>		
		{foreach $meals as $meal}
			{if {$meal.kalenderwoche} eq {$smarty.now|date_format:"%W"+1}}
				{if {$meal.date} eq {$nextWednesday}} 
					<ul><a href="index.php?section=order&order={$meal.ID}">{$meal.name}</a></ul>
				{/if}
			{/if}
		{/foreach}
		</p></td>
		
<!-- donnerstag -->
<td>
		{foreach $meals as $meal}
			{if {$meal.kalenderwoche} eq {$smarty.now|date_format:"%W"+1}}
				{if {$meal.date} eq {$nextThursday}} 
					<ul><a href="index.php?section=order&order={$meal.ID}">{$meal.name}</a></ul>
				{/if}
			{/if}
		{/foreach}
		</p></td>
		
<!-- freitag -->
<td>
		{foreach $meals as $meal}
			{if {$meal.kalenderwoche} eq {$smarty.now|date_format:"%W"+1}}
				{if {$meal.date} eq {$nextFriday}} 
					<ul><a href="index.php?section=order&order={$meal.ID}">{$meal.name}</a></ul>
				{/if}
			{/if}
		{/foreach}
		</p></td>
	</tr>
</table>
<!-- oben die Wochentage, links men� 1, men� 2     -->
{include file='web/footer.tpl'}
