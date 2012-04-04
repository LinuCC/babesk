{extends file=$base_path}{block name=content}
Geben sie bitte das Datum ein, wofür sie die bisher eingegangenen Bestellungen mit Teilhabepaket angezeigt haben möchten:<br>

<form action="index.php?section=soli&amp;action=4" method="post">
	Kalenderwoche:<select name="ordering_kw">
	{section name=i loop=52}
	<option value="{{$smarty.section.i.index}+1}"> {{$smarty.section.i.index}+1}</option>
	{/section}
	</select> (Aktuell: Kalenderwoche {date('W')}) <br>
	
	Einen Teilhabeberechtigten auswählen:<br>
	<select name="user_id">
	{foreach $solis as $soli}
		<option value='{$soli.ID}'>{$soli.forename} {$soli.name}</option>
	{/foreach}
	</select><br>
 
	<input type="submit" value="Anzeigen" />
</form>

{/block}