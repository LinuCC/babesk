{include file='web/header.tpl' title='Bestellen'}

<p>
	<u>Speiseplan:</u>
</p>
<!-- 
{literal}
//////////////////////////////////////////////////

 #This Code is a placeholder for a refactor of order.tpl and order.php 

<table>
	<tr>
		<td><p>
				Montag<br>{$thisMonday}
			</p></td>
		<td><p>
				Dienstag<br>{$thisTuesday}
			</p></td>
		<td><p>
				Mittwoch<br>{$thisWednesday}
			</p></td>
		<td><p>
				Donnerstag<br>{$thisThursday}
			</p></td>
		<td><p>
				Freitag<br>{$thisFriday}
			</p></td>
	</tr>
	
	{foreach $mealweek as $meals} 
		<tr>
			{foreach $meals as $meal}
				<td>
					<a href="index.php?section=order&order={$meal.ID}">{$meal.name}</a>
				</td>
			{/foreach}
		</tr>
	{/foreach}

</table>

{/literal}
//////////////////////////////////////////////////-->

<center><p>Diese Woche</p>{$message}</center>
<table>
		
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
