{include file='web/header.tpl' title='Bestellen'}

<p>
	<u>Speiseplan:</u>
</p>
<center><p>Diese Woche</p>{$message}</center>
<table>
{assign var="thisMonday" value="last Monday"|date_format:"%d.%m.%Y"}
		{assign var="thisTuesday" value="last Tuesday"|date_format:"%d.%m.%Y"}
		{assign var="thisWednesday" value="last Wednesday"|date_format:"%d.%m.%Y"}
		{assign var="thisThursday" value="last Thursday"|date_format:"%d.%m.%Y"}
		{assign var="thisFriday" value="last Friday"|date_format:"%d.%m.%Y"}

		{if {$smarty.now|date_format:"%w"} eq 1} 
		 {$thisMonday={$smarty.now|date_format:"%d.%m.%Y"}}
		 {$thisTuesday="next Tuesday"|date_format:"%d.%m.%Y"}
		  {$thisWednesday="next Wednesday"|date_format:"%d.%m.%Y"} 
		   {$thisThursday="next Thursday"|date_format:"%d.%m.%Y"} 
		    {$thisFriday="next Friday"|date_format:"%d.%m.%Y"}  
		{/if} 
		{if {$smarty.now|date_format:"%w"} eq 2} 
		 {$thisTuesday={$smarty.now|date_format:"%d.%m.%Y"}} 
		  {$thisWednesday="next Wednesday"|date_format:"%d.%m.%Y"} 
		   {$thisThursday="next Thursday"|date_format:"%d.%m.%Y"} 
		    {$thisFriday="next Friday"|date_format:"%d.%m.%Y"} 
		{/if} 
		{if {$smarty.now|date_format:"%w"} eq 3} 
		 {$thisWednesday={$smarty.now|date_format:"%d.%m.%Y"}} 
		  {$thisThursday="next Thursday"|date_format:"%d.%m.%Y"} 
		   {$thisFriday="next Friday"|date_format:"%d.%m.%Y"} 
		{/if} 
		{if {$smarty.now|date_format:"%w"} eq 4} 
		 {$thisthursday={$smarty.now|date_format:"%d.%m.%Y"}} 
		  {$thisFriday="next Friday"|date_format:"%d.%m.%Y"} 
		{/if} 
		{if {$smarty.now|date_format:"%w"} eq 5} 
		 {$thisFriday={$smarty.now|date_format:"%d.%m.%Y"}} 
		{/if} 
		<tr><td><p>Montag<br>{$thisMonday}</td><td><p>Dienstag<br>{$thisTuesday}</td>
	<td><p>Mittwoch<br>{$thisWednesday}</td><td><p>Donnerstag<br>{$thisThursday}</td>
			<td><p>Freitag<br>{$thisFriday}</td></tr>	
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
</table>
	<center><p>N&auml;chste Woche</p>{$message}</center>
	<table>
	
	{assign var="nextMonday" value="next Monday"|date_format:"%d.%m.%Y"}
		{assign var="nextTuesday" value="next Tuesday"|date_format:"%d.%m.%Y"}
		{assign var="nextWednesday" value="next Wednesday"|date_format:"%d.%m.%Y"}
		{assign var="nextThursday" value="next Thursday"|date_format:"%d.%m.%Y"}
		{assign var="nextFriday" value="next Friday"|date_format:"%d.%m.%Y"}
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
