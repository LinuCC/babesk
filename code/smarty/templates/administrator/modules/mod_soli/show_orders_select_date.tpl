Geben sie bitte das Datum ein, wofür sie die bisher eingegangenen Bestellungen mit Teilhabepaket angezeigt haben möchten:<br>

<form action="index.php?section=soli&amp;action=1" method="post">
	<label>Tag:<input type="text" name="ordering_day" maxlength="2" size="2" value={$today.day} /></label>
	<label>Monat:<input type="text" name="ordering_month" maxlength="2" value={$today.month} size="2" /></label>
	<label>Jahr:<input type="text" name="ordering_year" maxlength="4" value={$today.year} size="4" /></label><br>
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
