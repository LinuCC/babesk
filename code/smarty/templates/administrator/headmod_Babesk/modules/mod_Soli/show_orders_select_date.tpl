{extends file=$base_path}{block name=content}
Geben Sie bitte das Datum ein, wofür sie die bisher eingegangenen Bestellungen mit Teilhabepaket angezeigt haben möchten:<br>

<form action="index.php?section=Babesk|Soli&amp;action=4" method="post">
	Kalenderwoche:<select name="ordering_kw">
	{section name=i loop=52}
	<option value="{{$smarty.section.i.index}+1}"  {if {{$smarty.section.i.index}+1} == {date('W')}} selected{/if}> {{$smarty.section.i.index}+1} </option>
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