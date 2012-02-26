Geben sie bitte das Datum ein, wofür sie die bisher eingegangenen Bestellungen mit Teilhabepaket angezeigt haben möchten:<br>

<form action="index.php?section=soli&amp;action=1" method="post">
	Kalenderwoche:<select name="ordering_kw">
	{section name=i loop=52}
	<option value="{{$smarty.section.i.index}+1}"> {{$smarty.section.i.index}+1}</option>
	{/section}
	</select> (Aktuell: Kalenderwoche {$today.week}) <br>
	
	Name:<select name="name">
	{foreach item=x from=$solis}
	<option value='{$x}'> {$x}</option>
	{/foreach}
	</select><br>
Teilhabeberechtigt:
{foreach $soli_orders as $user}
 {$user.forename} {$num_order.name}<br>
 
{/foreach}

	Kalenderwoche:<select name="kw">
  
	{section name=i loop=52}
<option value="{{$smarty.section.i.index}+1}">{{$smarty.section.i.index}+1}</option>
{/section}
	
	<input type="submit" value="Anzeigen" />
</form>
