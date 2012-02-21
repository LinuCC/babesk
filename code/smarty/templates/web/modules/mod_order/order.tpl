{literal}
<script type="text/javascript">
function ShowHideDiv(divName){
	 //Gibt es das Objekt mit dem Namen der in divName übergeben wurde?
	 if(document.getElementById(divName)){
	  /*"Sichtbarkeit" des Divs umschalten. 
	  Wenn es sichtbar war, unsichtbar machen und umgedreht.*/
	  document.getElementById(divName).style.display = 
	   (document.getElementById(divName).style.display == 'none') ? 'inline' : 'none';
	 }
}

</script>
{/literal}


{include file='web/header.tpl' title='Bestellen'}

<h2>
	<u>Speiseplan:</u>
</h2>

{literal}
<style type="text/css">
th {width:20%; background-color:#84ff00; text-align: center;}
td {width:20%; background-color:#f8f187; text-align: center;}
table{width:100%;}
</style>
{/literal}
<center><h3>Diese Woche</h3>{$message}</center>
<table width="100%">
		
		<tr><th><p>Montag<br>{$thisMonday}</th><th ><p>Dienstag<br>{$thisTuesday}</th>
	<th ><p>Mittwoch<br>{$thisWednesday}</th><th ><p>Donnerstag<br>{$thisThursday}</th>
			<th ><p>Freitag<br>{$thisFriday}</th></tr>	
	<!-- montag -->
		<tr>
		<td>
		{foreach $meals as $meal}
			{if {$meal.kalenderwoche} eq {$smarty.now|date_format:"%W"}}
				{if {$meal.date} eq {$thisMonday}} 
					<ul><a href="index.php?section=order&order={$meal.ID}" onmouseover="javascript:ShowHideDiv('thisMondayDiv')">{$meal.name}</a></ul>
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
	
	<tr><th><p>Montag<br>{$nextMonday}</th><th><p>Dienstag<br>{$nextTuesday}</th>
	<th><p>Mittwoch<br>{$nextWednesday}</th><th><p>Donnerstag<br>{$nextThursday}</th>
			<th><p>Freitag<br>{$nextFriday}</th></tr>
		
<!-- montag -->
		<tr>
		<td>
		{foreach $meals as $meal}
			{if {$meal.kalenderwoche} eq {$smarty.now|date_format:"%W"+1}}
				{if {$meal.date} eq {$nextMonday}} 
					<ul><a href="index.php?section=order&order={$meal.ID}" onmouseover="javascript:ShowHideDiv('thisMondayDiv')">{$meal.name}</a></ul>
					 <div  id="thisMondayDiv" style="display:none;">ICH MAG SCHINKEN</div
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
